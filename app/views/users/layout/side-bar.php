<ul class="nav nav-pills flex-column mb-auto gap-1">
                    
                    
                    <?php if ($_SESSION['role_id'] == 1): ?>
                        <li class="nav-item">
                            <a href="?url=admin-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'admin-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-coaches" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>Administrar Profesores</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=admin-manage-swimmers" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Administrar Nadadores</span>
                            </a>
                        </li>
                    
                    <?php elseif ($_SESSION['role_id'] == 2): ?>
                        <li class="nav-item">
                            <a href="?url=coach-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'coach-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=coach-profile" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-speedometer2 fs-5"></i>
                                <span>Gestión del perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-table fs-5"></i>
                                <span>Gestión de clases</span>
                            </a>
                        </li>

                    <?php elseif ($_SESSION['role_id'] == 3): ?>
                        <li class="nav-item">
                            <a href="?url=swimmer-home" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'swimmer-home') ? 'active bg-primary' : 'opacity-75'; ?>" aria-current="page">
                                <i class="bi bi-house-door fs-5"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=swimmer-profile" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'profile') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span>Mi Perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="?url=swimmer-lessons" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'lessons') ? 'active bg-primary' : 'opacity-75'; ?>">
                                <i class="bi bi-journal-bookmark-fill fs-5"></i>
                                <span>Mis Clases</span>
                            </a>
                        </li>                    
                    
                     <?php elseif ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3): ?>
                    <li>
                        <a href="" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'calendar') ? 'active bg-primary' : 'opacity-75'; ?>">
                            <i class="bi bi-grid-3x3-gap fs-5"></i>
                            <span>Calendario</span>
                        </a>
                    </li>

                    <li>
                    <a class="nav-link btn btn-outline-danger btn-sm ms-lg-3 w-100 w-lg-auto" href="?url=logout" id="btnLogout">
                        <i class="bi bi-grid-3x3-gap fs-5"></i>
                        <span>Salir</span>
                    </a>
                    </li>

                     

                </ul>
                <?php endif; ?>