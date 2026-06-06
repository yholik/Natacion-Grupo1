<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="d-flex flex-column flex-lg-row w-100">
    <div class="d-flex">
        <aside class="p-3 text-white bg-dark d-none d-lg-block flex-shrink-0" style="width: 280px; min-height: 100vh;">
            <?php include __DIR__ . '/../layout/side-bar.php'; ?>
        </aside>
    </div>

    <div class="flex-grow-1 p-5 bg-white">
        <h1>Agregar Profesor</h1>
        <hr>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                Datos del Profesor
            </div>

            <div class="card-body">
                <form
                    id="formCrearCoach"
                    action="<?= htmlspecialchars(Env::get('APP_URL'), ENT_QUOTES, 'UTF-8') ?>/?url=admin-create-coach"
                    method="POST"
                >
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="col-md-12">
                            <label for="specialty" class="form-label">Especialidad</label>
                            <input type="text" class="form-control" id="specialty" name="specialty" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success" id="btnGuardarCoach">
                            Guardar
                        </button>

                        <a href="<?= htmlspecialchars(Env::get('APP_URL'), ENT_QUOTES, 'UTF-8') ?>/?url=admin-manage-coaches" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal genérico reutilizable -->
        <?php require_once __DIR__ . '/../../components/modal_confirmacion.php'; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Formulario de creación del profesor.
        const formCrearCoach = document.getElementById('formCrearCoach');

        // Botón guardar. Se usa para deshabilitarlo mientras se procesa.
        const btnGuardarCoach = document.getElementById('btnGuardarCoach');

        formCrearCoach.addEventListener('submit', async (event) => {
            // Evita que el formulario recargue la página automáticamente.
            // En su lugar, se envía con fetch.
            event.preventDefault();

            // Bloquea el botón para evitar doble envío.
            btnGuardarCoach.disabled = true;
            btnGuardarCoach.textContent = 'Guardando...';

            try {
                // Toma todos los campos del formulario.
                const formData = new FormData(formCrearCoach);

                // Envía el formulario por POST al controlador.
                const response = await fetch(formCrearCoach.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        // Marca la petición como AJAX.
                        // Sirve si luego querés diferenciar peticiones normales de fetch.
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Lee el tipo de respuesta enviada por el servidor.
                const contentType = response.headers.get('content-type') || '';

                // Lee la respuesta como texto para poder diagnosticar errores.
                const rawResponse = await response.text();

                // Si el servidor no devuelve JSON, algo está mal en la ruta o en el controlador.
                if (!contentType.includes('application/json')) {
                    console.error('Respuesta no JSON del servidor:', rawResponse);

                    await mostrarConfirmacion(
                        'No se pudo crear el profesor. El servidor devolvió una respuesta inválida.',
                        {
                            titulo: 'Error',
                            textoAceptar: 'Aceptar',
                            claseAceptar: 'btn-danger',
                            mostrarCancelar: false
                        }
                    );

                    return;
                }

                // Convierte el texto JSON en objeto JavaScript.
                const result = JSON.parse(rawResponse);

                // Compatibilidad con distintas estructuras de respuesta.
                const status = result.status || result.type || '';
                const message = result.message || 'Operación finalizada.';
                const redirectUrl = result.redirect || result.url || null;

                // Caso exitoso.
                if (status === 'success') {
                    await mostrarConfirmacion(
                        message,
                        {
                            titulo: 'Profesor creado',
                            textoAceptar: 'Aceptar',
                            claseAceptar: 'btn-success',
                            mostrarCancelar: false
                        }
                    );

                    // Si el controlador envió una URL, redirige al listado.
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    }

                    return;
                }

                // Caso donde el servidor responde JSON, pero no fue success.
                await mostrarConfirmacion(
                    message || 'No se pudo crear el profesor.',
                    {
                        titulo: 'No se pudo crear',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    }
                );

            } catch (error) {
                // Errores de red, errores JS o problemas inesperados.
                console.error(error);

                await mostrarConfirmacion(
                    'No se pudo crear el profesor. Revisá la conexión, la ruta o el error del servidor.',
                    {
                        titulo: 'Error',
                        textoAceptar: 'Aceptar',
                        claseAceptar: 'btn-danger',
                        mostrarCancelar: false
                    }
                );

            } finally {
                // Siempre se vuelve a habilitar el botón, haya salido bien o mal.
                btnGuardarCoach.disabled = false;
                btnGuardarCoach.textContent = 'Guardar';
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>