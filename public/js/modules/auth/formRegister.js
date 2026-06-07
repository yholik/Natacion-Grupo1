import { handleAlert } from "../../services/ui.js";
import { initCropper } from "../cropperMain.js";

export function initRegister() {
  const form = document.getElementById("formRegister");

  if (!form) return;

  const fileInput = form.querySelector('input[name="profile_image"]');
  const cropper = initCropper(fileInput, { aspectRatio: 1 });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    if (cropper) {
      const croppedFile = cropper.getCroppedFile();
      if (croppedFile) {
        formData.set('profile_image', croppedFile, croppedFile.name);
      }
    }

    try {
      const response = await fetch("?url=register", {
        method: "POST",
        body: formData,
      });

      const text = await response.text();
      console.log("text antes del try: ", text);
      try {
        const data = JSON.parse(text);
        console.log("data antes del handleAlert: ", data);
        // El servidor retornará el status (success, error, warning) y el mensaje
        handleAlert(data.status, data.message, data.redirect);
      } catch (err) {
        // En caso de un error crítico de PHP (Fatal Error), la respuesta no será un JSON válido
        console.error("Respuesta inesperada del servidor:", text);
        handleAlert(
          "error",
          "Error crítico en el servidor. Revisar consola de red.",
        );
      }
      /*     try {
        // Limpiamos espacios en blanco accidentales que pueda mandar PHP
        const cleanText = text.trim();
        console.log("Contenido crudo recibido:", text);

        const data = JSON.parse(cleanText);
        console.log("JSON parseado con éxito:", data);

        handleAlert(data.status, data.message, data.redirect);
      } catch (err) {
        console.error("ERROR DE PARSEO:");
        console.error(err.message);
        console.error("LO QUE FALLÓ FUE ESTO ->", text);
        handleAlert(
          "error",
          "El servidor mandó un formato inválido. Revisar consola.",
        );
      } */
    } catch (error) {
      console.error("Error en la conexión Fetch:", error);
      handleAlert("error", "No se pudo establecer conexión con el servidor.");
    }
  });
}
