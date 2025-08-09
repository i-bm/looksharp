

	<!-- jQuery -->

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>

	<!-- Feather Icon JS -->
	<script src="{{ asset('assets/js/feather.min.js')}}"></script>

	<!-- Custom JS -->
	<script src="{{ asset('assets/js/script.js')}}"></script>
    <script>
        $('form').on('submit', function(){
            $('.submit-btn').attr("disabled", true).text('Please wait...');
        });
    </script>
</body>

</html>