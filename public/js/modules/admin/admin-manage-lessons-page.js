import { initCalendar } from "../calendar.js";

export function initAdminManageLessonsPage() {
    const config = window.adminManageLessonsConfig;
    const lessonForm = document.getElementById("lessonForm");

    if (!config || !lessonForm) {
        return;
    }

    const { lessons, dayMap, dayNames, appUrl } = config;

    const lessonModalElement = document.getElementById("lessonModal");
    const detailModalElement = document.getElementById("detailModal");
    const lessonModalTitle = document.getElementById("lessonModalTitle");
    const lessonSubmitButton = document.getElementById("lessonSubmitButton");
    const lessonIdInput = document.getElementById("lessonId");
    const lessonDayInput = document.getElementById("lessonDay");
    const lessonDayLabel = document.getElementById("lessonDayLabel");
    const coachSelect = document.getElementById("coach_id");
    const specialtySelect = document.getElementById("specialty_id");
    const specialtyHidden = document.getElementById("specialty_id_hidden");
    const levelSelect = document.getElementById("level_id");
    const levelHidden = document.getElementById("level_id_hidden");
    const startTimeSelect = document.getElementById("createTime");
    const endTimeSelect = document.getElementById("endTime");
    const capacityInput = document.getElementById("capacity");
    const detailEditButton = document.getElementById("detailEditButton");
    const detailDeleteButton = document.getElementById("detailDeleteButton");

    let selectedLesson = null;
    let allSpecialties = [];
    let allCoachOptions = [];

    function syncHiddenInputs() {
        specialtyHidden.value = specialtySelect.value;
        levelHidden.value = levelSelect.value;
    }

    function filterCoachesBySpecialty(specialtyId) {
        const currentCoach = coachSelect.value;
        const firstOption = coachSelect.querySelector("option:first-child");
        coachSelect.innerHTML = "";
        coachSelect.appendChild(firstOption);

        allCoachOptions.forEach((opt) => {
            if (!specialtyId) {
                coachSelect.appendChild(opt.cloneNode(true));
            } else {
                const ids = (opt.getAttribute("data-specialty-ids") || "").split(",").map((s) => s.trim());
                if (ids.includes(String(specialtyId))) {
                    coachSelect.appendChild(opt.cloneNode(true));
                }
            }
        });

        if (currentCoach && coachSelect.querySelector(`option[value="${currentCoach}"]`)) {
            coachSelect.value = currentCoach;
        } else {
            coachSelect.value = "";
        }
    }

    function populateSpecialtySelect(specialties, preserveSelected) {
        const prev = preserveSelected ? specialtySelect.value : "";
        specialtySelect.innerHTML = '<option value="">Seleccionar especialidad...</option>';
        specialties.forEach((s) => {
            const opt = document.createElement("option");
            opt.value = s.id;
            opt.textContent = s.name;
            specialtySelect.appendChild(opt);
        });
        if (preserveSelected && prev && specialties.some((s) => String(s.id) === prev)) {
            specialtySelect.value = prev;
        } else {
            specialtySelect.value = "";
        }
        syncHiddenInputs();
    }

    async function fetchCoachSpecialties(coachProfileId) {
        if (specialtySelect.disabled) {
            return;
        }
        if (!coachProfileId) {
            populateSpecialtySelect(allSpecialties, false);
            return;
        }
        try {
            const res = await fetch(appUrl + "/?url=admin-get-coach-specialties&coach_id=" + coachProfileId);
            const json = await res.json();
            if (json.status === "success" && json.data.specialties) {
                populateSpecialtySelect(json.data.specialties, true);
            } else {
                populateSpecialtySelect([], false);
            }
        } catch (err) {
            console.error("Error al cargar especialidades del profesor.", err);
            populateSpecialtySelect([], false);
        }
    }

    coachSelect.addEventListener("change", () => {
        fetchCoachSpecialties(coachSelect.value);
    });

    specialtySelect.addEventListener("change", syncHiddenInputs);
    levelSelect.addEventListener("change", syncHiddenInputs);

    // Ajusta los horarios de fin según la hora de inicio elegida.
    function updateEndTimeOptions() {
        const startTime = startTimeSelect.value;
        const startHour = parseInt(startTime.substring(0, 2), 10);

        Array.from(endTimeSelect.options).forEach((option) => {
            const optionHour = parseInt(option.value.substring(0, 2), 10);
            option.disabled = optionHour <= startHour;
        });

        const nextHour = String(startHour + 1).padStart(2, "0") + ":00:00";
        if (endTimeSelect.value <= startTime) {
            endTimeSelect.value = nextHour;
        }
    }

    function openLessonModal() {
        bootstrap.Modal.getOrCreateInstance(lessonModalElement).show();
    }

    function closeLessonModal() {
        bootstrap.Modal.getOrCreateInstance(lessonModalElement).hide();
    }

    function closeDetailModal() {
        bootstrap.Modal.getOrCreateInstance(detailModalElement).hide();
    }

    // Prepara el formulario para una clase nueva.
    function setupCreateMode(dayKey, dayName) {
        lessonForm.reset();
        lessonForm.action = appUrl + "/?url=admin-create-lesson";
        lessonModalTitle.textContent = "Agregar clase";
        lessonSubmitButton.textContent = "Crear clase";
        lessonIdInput.value = "";
        lessonDayInput.value = dayKey;
        lessonDayLabel.textContent = dayName;
        startTimeSelect.value = "08:00:00";
        capacityInput.value = "1";
        coachSelect.value = "";
        specialtySelect.value = "";
        levelSelect.value = "";
        specialtySelect.disabled = false;
        levelSelect.disabled = false;
        populateSpecialtySelect(allSpecialties, false);
        filterCoachesBySpecialty(null);
        updateEndTimeOptions();
        syncHiddenInputs();
    }

    // Carga los datos de una clase para editarla.
    async function setupEditMode(lesson) {
        lessonForm.reset();
        lessonForm.action = appUrl + "/?url=admin-edit-lesson";
        lessonModalTitle.textContent = "Editar clase";
        lessonSubmitButton.textContent = "Guardar cambios";
        lessonIdInput.value = lesson.id || "";
        lessonDayInput.value = lesson.day_of_week || "";
        lessonDayLabel.textContent = dayNames[dayMap[lesson.day_of_week]] || "";
        startTimeSelect.value = lesson.start_time || "08:00:00";
        updateEndTimeOptions();
        endTimeSelect.value = lesson.end_time || "";
        capacityInput.value = lesson.capacity || 1;

        const hasEnrolled = (lesson.enrolled || 0) > 0;
        specialtySelect.disabled = hasEnrolled;
        levelSelect.disabled = hasEnrolled;

        if (!hasEnrolled) {
            await fetchCoachSpecialties(lesson.coach_id || "");
        }
        specialtySelect.value = lesson.specialty_id || "";
        levelSelect.value = lesson.level_id || "";
        filterCoachesBySpecialty(hasEnrolled ? lesson.specialty_id : null);
        coachSelect.value = lesson.coach_id || "";
        syncHiddenInputs();
    }

    // Muestra un mensaje informativo usando el modal compartido.
    async function showMessage(title, message, acceptClass = "btn-primary") {
        await window.mostrarConfirmacion(message, {
            titulo: title,
            textoAceptar: "Aceptar",
            claseAceptar: acceptClass,
            mostrarCancelar: false
        });
    }

    // Envía el alta o edición al backend.
    async function submitLessonForm(event) {
        event.preventDefault();

        const isEditing = lessonIdInput.value !== "";
        lessonSubmitButton.disabled = true;
        lessonSubmitButton.textContent = isEditing ? "Guardando..." : "Creando...";

        try {
            const response = await fetch(lessonForm.action, {
                method: "POST",
                body: new FormData(lessonForm),
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const result = await response.json();

            if (result.status === "success") {
                closeLessonModal();
                await showMessage(
                    isEditing ? "Clase actualizada" : "Clase creada",
                    result.message || "Operación finalizada.",
                    "btn-success"
                );
                window.location.reload();
                return;
            }

            await showMessage(
                isEditing ? "No se pudo actualizar" : "No se pudo crear",
                result.message || "No se pudo guardar la clase.",
                "btn-danger"
            );
        } catch (error) {
            console.error("Error al guardar la clase.", error);
            await showMessage("Error", "No se pudo guardar la clase. Revisá la conexión o la respuesta del servidor.", "btn-danger");
        } finally {
            lessonSubmitButton.disabled = false;
            lessonSubmitButton.textContent = isEditing ? "Guardar cambios" : "Crear clase";
        }
    }

    // Pide confirmación y borra la clase elegida.
    async function deleteSelectedLesson() {
        if (!selectedLesson) {
            return;
        }

        const enrolled = selectedLesson.enrolled || 0;
        if (enrolled > 0) {
            await showMessage(
                "Clase con inscriptos",
                `No se puede eliminar la clase porque tiene ${enrolled} alumno(s) inscripto(s) actualmente. Primero desinscribilos.`,
                "btn-primary"
            );
            return;
        }

        const confirmed = await window.mostrarConfirmacion(
            `Se eliminará la clase de ${selectedLesson.specialty_name || "sin especialidad"} del ${dayNames[dayMap[selectedLesson.day_of_week]]}.`,
            {
                titulo: "Eliminar clase",
                textoAceptar: "Eliminar",
                textoCancelar: "Cancelar",
                claseAceptar: "btn-danger"
            }
        );

        if (!confirmed) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append("lesson_id", selectedLesson.id);

            const response = await fetch(appUrl + "/?url=admin-delete-lesson", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });

            const result = await response.json();

            if (result.status === "success") {
                closeDetailModal();
                await showMessage("Clase eliminada", result.message || "Clase eliminada correctamente.", "btn-success");
                window.location.reload();
                return;
            }

            await showMessage("No se pudo eliminar", result.message || "No se pudo eliminar la clase.", "btn-danger");
        } catch (error) {
            console.error("Error al eliminar la clase.", error);
            await showMessage("Error", "No se pudo eliminar la clase. Revisá la conexión o la respuesta del servidor.", "btn-danger");
        }
    }

    // Completa el modal detalle con la clase elegida.
    function showLessonDetail(dayIdx, lesson) {
        selectedLesson = lesson;

        const start = lesson.start_time.substring(0, 5);
        const end = lesson.end_time.substring(0, 5);
        const enrolled = lesson.enrolled || 0;
        const coachName = `${lesson.coach_first_name || ""} ${lesson.coach_last_name || ""}`.trim();

        document.getElementById("detailCoach").textContent = coachName || "Sin profesor";
        document.getElementById("detailLevel").textContent = lesson.level_name || "-";
        document.getElementById("detailSchedule").textContent = `${dayNames[dayIdx]} ${start} - ${end}`;
        document.getElementById("detailSpecialty").textContent = lesson.specialty_name || "Sin especialidad";
        document.getElementById("detailCapacity").textContent = `${enrolled}/${lesson.capacity}`;

        bootstrap.Modal.getOrCreateInstance(detailModalElement).show();
    }

    startTimeSelect.addEventListener("change", updateEndTimeOptions);
    lessonForm.addEventListener("submit", submitLessonForm);

    detailEditButton.addEventListener("click", () => {
        if (!selectedLesson) {
            return;
        }

        closeDetailModal();
        setupEditMode(selectedLesson);
        openLessonModal();
    });

    detailDeleteButton.addEventListener("click", deleteSelectedLesson);

    // Guarda todas las especialidades al cargar la pagina.
    allSpecialties = Array.from(specialtySelect.options)
        .filter((opt) => opt.value !== "")
        .map((opt) => ({ id: opt.value, name: opt.textContent }));

    // Guarda todas las opciones de profesores al cargar la pagina.
    allCoachOptions = Array.from(coachSelect.options)
        .filter((opt) => opt.value !== "")
        .map((opt) => opt.cloneNode(true));

    initCalendar({
        data: lessons,
        dayMap,
        dayNames,
        cardButtonLabel: "Ver detalle",
        onCardClick: (dayIdx, lesson) => showLessonDetail(dayIdx, lesson),
        onAddClick: (dayKey, dayName) => {
            setupCreateMode(dayKey, dayName);
            openLessonModal();
        }
    });
}

initAdminManageLessonsPage();
