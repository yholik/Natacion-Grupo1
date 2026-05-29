<link rel="stylesheet" href="<?= Env::get('ASSET_URL') ?>/css/coach.css">
<!-- ASIDE -->
<div class="d-flex" id="navsidebar">
<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; min-height: 100vh;">
  
  <a href="coach-home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none gap-2">
    <i class="bi bi-bootstrap-fill fs-2"></i>
    <span class="fs-4 tracking-tight">Panel de control</span>
  </a>
  
  <hr class="border-secondary my-3">

  <ul class="nav nav-pills flex-column mb-auto gap-1">
    
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
    
    <li>
      <a href="" class="nav-link text-white d-flex align-items-center gap-3 py-2.5 px-3 <?php echo ($current_page == 'calendar') ? 'active bg-primary' : 'opacity-75'; ?>">
        <i class="bi bi-grid-3x3-gap fs-5"></i>
        <span>Calendario</span>
      </a>
    </li>

  </ul>
</aside>
