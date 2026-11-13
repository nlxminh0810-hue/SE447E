<?php
// Sidebar Navigation
$currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
$currentAction = isset($_GET['action']) ? $_GET['action'] : '';

$menuItems = [
    ['icon' => '🏠', 'label' => 'Home', 'controller' => 'course', 'action' => ''],
    ['icon' => '📚', 'label' => 'Courses', 'controller' => 'course', 'action' => 'list'],
    ['icon' => '🗺️', 'label' => 'Learning Path', 'controller' => 'lesson', 'action' => 'index', 'course_id' => '1'],
    ['icon' => '📈', 'label' => 'Progress', 'controller' => 'progress', 'action' => ''],
    ['icon' => '🤖', 'label' => 'English Tutor', 'controller' => 'chat', 'action' => ''],
];

if (Auth::isAdmin()) {
    $menuItems[] = ['icon' => '🛠️', 'label' => 'Admin', 'controller' => 'admin', 'action' => 'index'];
}

$userMenuItems = [
    ['icon' => '👤', 'label' => 'Profile', 'controller' => 'profile', 'action' => ''],
    ['icon' => '⚙️', 'label' => 'Setting', 'controller' => 'setting', 'action' => ''],
];
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <span class="logo-text">E</span>
        </div>
        <span class="sidebar-title">English AI</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <ul class="nav-menu">
                <?php foreach ($menuItems as $item): 
                    $isActive = ($item['controller'] === $currentController && (string)$item['action'] === (string)$currentAction) ? 'active' : '';
                    $url = '?controller=' . $item['controller'];
                    if ($item['action']) {
                        $url .= '&action=' . $item['action'];
                    }
                    if (isset($item['course_id'])) {
                        $url .= '&course_id=' . $item['course_id'];
                    }
                ?>
                    <li class="nav-item <?php echo $isActive; ?>">
                        <a href="<?php echo $url; ?>" class="nav-link">
                            <span class="nav-icon"><?php echo $item['icon']; ?></span>
                            <span class="nav-label"><?php echo $item['label']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="nav-section nav-footer-section">
            <ul class="nav-menu">
                <?php foreach ($userMenuItems as $item): 
                    $isActive = ($item['controller'] === $currentController && (string)$item['action'] === (string)$currentAction) ? 'active' : '';
                    $url = '?controller=' . $item['controller'];
                    if ($item['action']) {
                        $url .= '&action=' . $item['action'];
                    }
                ?>
                    <li class="nav-item <?php echo $isActive; ?>">
                        <a href="<?php echo $url; ?>" class="nav-link">
                            <span class="nav-icon"><?php echo $item['icon']; ?></span>
                            <span class="nav-label"><?php echo $item['label']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                
                <?php if (Auth::isLoggedIn()): ?>
                    <li class="nav-item">
                        <a href="?controller=auth&action=logout" class="nav-link logout-link">
                            <span class="nav-icon">🚪</span>
                            <span class="nav-label">Log Out</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</aside>
