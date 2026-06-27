<?php $current_page = $_GET['url'] ?? ''; ?>
<?php // Menú lateral que cambia según el rol activo. ?>

<ul class="nav nav-pills flex-column mb-auto gap-1">
<?php if (isset($_SESSION['user_id'])): ?>
                    
                    <?php if ($_SESSION['role_id'] == 1): ?>
                        <li>
                            <a href="?url=admin-manage-coaches" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo in_array($current_page, ['admin-manage-coaches', 'admin-create-coach', 'admin-edit-coach'], true) ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>Administrar Profesores</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-swimmers" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo in_array($current_page, ['admin-manage-swimmers', 'admin-create-swimmer', 'admin-edit-swimmer'], true) ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Administrar Nadadores</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-lessons" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'admin-manage-lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Administrar Clases</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-specialties" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'admin-manage-specialties') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-tags fs-5"></i>
                                <span>Administrar Especialidades</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 2): ?>
                        <li class="nav-item">
                            <a href="?url=coach-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'coach-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=profile" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>Gestión del perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=coach-lessons" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Gestión de alumnos</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=coach-calendar" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'calendar') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-grid-3x3-gap fs-5"></i>
                                <span>Agenda/Calendario</span>
                            </a>
                        </li>
                        <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 3): ?>
                        <li class="nav-item">
                            <a href="?url=swimmer-classes-avaliable" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'swimmer-classes-avaliable') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Clases Disponibles</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=profile" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span>Mi Perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=swimmer-my-classes" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'swimmer-my-classes') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-journal-bookmark-fill fs-5"></i>
                                <span>Mis Clases</span>
                            </a>
                        </li>
                        <?php endif; ?>                
               

                    <li>
                    <a class="nav-link btn btn-outline-danger btn-sm ms-lg-3 w-100 w-lg-auto" href="?url=logout" id="btnLogout">
                        <i class="bi bi-grid-3x3-gap fs-5"></i>
                        <span>Salir</span>
                    </a>
                    </li>

                     
                
<?php endif; ?>
                </ul>
