import { initCoachHome } from "./coach/coachHome.js";
import { initCoachProfile } from "./coach/coachProfile.js";
import { initCoachStudents } from "./coach/coachStudents.js";

document.addEventListener('DOMContentLoaded', () => {
    initCoachHome();
    initCoachProfile();
    initCoachStudents();

    console.log("Coach modules loaded smoothly.");
})