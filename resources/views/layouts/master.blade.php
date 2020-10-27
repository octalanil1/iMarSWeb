<!DOCTYPE html>
@include('includes.head')
<body>
     <!-- Header -->
    @include('includes.header')
    <!-- Sidebar -->
    @yield('content')
    <!-- Footer -->
    @include('includes.footer')
    
</body>
</html>