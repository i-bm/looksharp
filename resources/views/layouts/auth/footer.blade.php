<!-- jQuery -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Bootstrap Core JS -->
<script src="{{ asset('assets/js/popper.min.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js')}}"></script>

<!-- Swiper JS -->
<script src="{{ asset('assets/js/swiper-bundle.min.js')}}"></script>

<!-- FancyBox JS (needed for custom.js) -->
<script src="{{ asset('assets/js/jquery.fancybox.min.js')}}"></script>

<!-- Custom JS -->
<script src="{{ asset('assets/js/custom.js')}}"></script>
<!-- Select Search Component JS -->
<script src="{{ asset('assets/js/select-search.js')}}"></script>
<!-- Autocomplete Component JS -->
<script src="{{ asset('assets/js/autocomplete.js') }}"></script>
<script>
    $('form').on('submit', function () {
            $(this).find('button[type="submit"], button').each(function () {
                $(this).attr("disabled", true).text('Please wait...');
            });
        });
</script>
@stack('scripts')
</body>

</html>