<!-- jQuery -->

<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

<!-- Popper and Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<!-- Swiper slider JS -->
<script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.js') }}"></script>
<!-- Waypoints JS -->
<script src="{{ asset('assets/js/waypoints.min.js') }}"></script>
<!-- Counterup JS -->
<script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
<!-- Nice JS -->
<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
<!-- Wow JS -->
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<!-- Gsap  JS -->
<script src="{{ asset('assets/js/gsap.min.js') }}"></script>
<script src="{{ asset('assets/js/ScrollTrigger.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.fancybox.min.js') }}"></script>
<!-- Custom JS -->
<script src="{{ asset('assets/js/select-dropdown.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<!-- Select Search Component JS -->
<script src="{{ asset('assets/js/select-search.js')}}"></script>
<!-- Autocomplete Component JS -->
<script src="{{ asset('assets/js/autocomplete.js') }}"></script>
<!-- Toaster JS -->
<script src="{{ asset('assets/js/toaster.js') }}"></script>
<!-- Dashboard JS -->
<script>
    // Sidebar Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.dashboard-sidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Navigation Dropdown Toggle
        const navLinks = document.querySelectorAll('.dashboard-nav-link-toggle');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.dashboard-nav-item-parent');
                if (parent) {
                    parent.classList.toggle('active');
                }
            });
        });

        // Mobile Sidebar Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleMobileSidebar() {
            if (sidebar && sidebarBackdrop) {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            }
        }

        if (mobileMenuBtn && sidebar) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                // On mobile, show/hide sidebar
                if (window.innerWidth <= 992) {
                    toggleMobileSidebar();
                }
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                toggleMobileSidebar();
            });
        }

        // Close sidebar when clicking outside on mobile
        if (window.innerWidth <= 992) {
            document.addEventListener('click', function(e) {
                if (sidebar && sidebarBackdrop &&
                    !sidebar.contains(e.target) &&
                    !mobileMenuBtn?.contains(e.target) &&
                    sidebar.classList.contains('show')) {
                    toggleMobileSidebar();
                }
            });
        }

        // Prevent collapse on mobile - only show/hide
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 992 && sidebar) {
                sidebar.classList.remove('collapsed');
            }
        });
    });
</script>

@stack('scripts')

</body>
<!-- [Body] end -->

</html>