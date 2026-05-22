/**
 * Servicio de Interfaz de Usuario
 * Centraliza el uso de SweetAlert2 para que los módulos no dependan
 * directamente de la librería y para estandarizar las respuestas del sistema.
 */

export const handleAlert = (status, message, redirectUrl = null) => {
  // Definimos la configuración base según el estado (status) que devuelve el servidor
  switch (status) {
    case "user_exists":
      /**
       * Caso especial: El usuario ya tiene cuenta.
       * Usamos allowOutsideClick: false para forzar al usuario a entender
       * que debe ir al login en lugar de intentar registrarse de nuevo.
       */
      Swal.fire({
        icon: "info",
        title: "Usuario Registrado",
        text: message,
        confirmButtonText: "Ir a Login",
        confirmButtonColor: "#0d6efd",
        allowOutsideClick: false,
        /*     }).then((result) => {
        if (result.isConfirmed && redirectUrl) {  
          window.location.href=redirectUrl;
        }
      }); */
      }).then((result) => {
        if (result.isConfirmed) {
          console.log("Redirigiendo a:", redirectUrl); // Mirá qué sale acá en la consola
          window.location.href = "http://localhost/gestion-natacion-grupo1/?url=login";
        }
      });
      break;

    case "success":
      /**
       * Operación exitosa. Si hay una URL de redirección,
       * esperamos a que el usuario cierre la alerta para navegar.
       */
      Swal.fire({
        icon: "success",
        title: "Success!",
        text: message,
      }).then(() => {
        if (redirectUrl) {
          window.location.href = redirectUrl;
        }
      });
      break;

    case "error":
      // Errores críticos (ej: fallo en la base de datos o excepción)
      Swal.fire("System Error", message, "error");
      break;

    case "warning":
      // Validaciones de formulario (ej: campos vacíos, contraseñas cortas)
      Swal.fire("Attention", message, "warning");
      break;

    default:
      // Avisos generales
      Swal.fire("Notice", message, "info");
      break;
  }
};
