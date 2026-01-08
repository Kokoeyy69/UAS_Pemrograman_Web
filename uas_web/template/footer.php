</div> </div> <footer class="bg-white py-3 border-top mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright &copy; <?= date('Y'); ?> Modular System</div>
                <div>
                    <a href="#" class="text-decoration-none text-muted me-3">Kebijakan Privasi</a>
                    <a href="#" class="text-decoration-none text-muted">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // 1. Script Auto-Close Alert (Pesan Notifikasi hilang sendiri)
        window.setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);

        // 2. SCRIPT TOGGLE SIDEBAR MOBILE (WAJIB ADA)
        // Ini adalah otak dari fitur responsive sidebar di HP
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if(toggleBtn && sidebar) {
                // Saat tombol menu diklik -> Buka/Tutup Sidebar
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Mencegah klik tembus
                    sidebar.classList.toggle('show'); 
                });

                // Saat layar disentuh di luar sidebar -> Tutup Sidebar
                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>