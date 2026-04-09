<?php if (!empty($_SESSION['usuario_id'])): ?>
        </section>
    </main>
</div>
<?php endif; ?>
<script src="assets/js/app.js"></script>
<?php
$pageJs = [];
if ($currentController === 'auditoria') {
    $pageJs[] = 'assets/js/admin.js';
}
if ($currentController === 'reporte') {
    $pageJs[] = 'assets/js/reportes.js';
}
if ($currentController === 'medico' && in_array($currentAction, ['crear', 'editar'], true)) {
    $pageJs[] = 'assets/js/medico-form.js';
}
foreach ($pageJs as $jsFile): ?>
<script src="<?= htmlspecialchars($jsFile) ?>"></script>
<?php endforeach; ?>
</body>
</html>

