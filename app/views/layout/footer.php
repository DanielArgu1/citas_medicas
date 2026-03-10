        </section>
    </main>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const toggleSidebar = document.getElementById('toggleSidebar');

    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        });
    }

    document.querySelectorAll('[data-count]').forEach((element) => {
        const target = parseInt(element.getAttribute('data-count'), 10) || 0;
        let current = 0;
        const increment = Math.max(1, Math.ceil(target / 40));

        const interval = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(interval);
            }
            element.textContent = current;
        }, 20);
    });
</script>

</body>
</html>