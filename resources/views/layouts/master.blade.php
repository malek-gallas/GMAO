
<!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>GMAO</title>
        @vite(['resources/sass/app.scss'])
    </head>


    <body class="hold-transition sidebar-mini">

        <div class="wrapper" id="app">

        <!-------------------------------------- Navbar --------------------------------------->
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">

            <!-- Expand/Shrink -->
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>

            <!-- Search -->
            <div class="input-group input-group-sm">
                <input v-model="search" @input="handleSearch" class="form-control" type="text" name="search" id="search" placeholder="Rechercher" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-navbar" @click="handleSearch">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>

        </nav>
        <!-------------------------------------- /.navbar --------------------------------------->

        <!--------------------------------- Main Sidebar Container ------------------------------>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/home" class="brand-link">
                <img src="./img/logo.jpg" alt="logo" class="brand-image img-circle elevation-3"style="opacity: .8">
                <span class="brand-text font-weight-light">GMAO</span>
            </a>

            <!-------------------------------------- Sidebar --------------------------------------->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="./img/admin.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div style="color:#DFEAEE" class="info">{{Auth::user()->first_name}}</div>
                    <div style="color:#DFEAEE" class="info">{{Auth::user()->last_name}}</div>
                    <div style="color:#DFEAEE" class="info">{{Auth::user()->role}}</div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    @if(Gate::allows('isAdmin') || Gate::allows('isManager'))       

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-sliders-h pink"></i>
                                <p>Ressources<i class="right fa fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">

                            @can('isAdmin') 
                                <li class="nav-item">
                                    <router-link to="/Utilisateurs" class="nav-link">
                                        <i class="nav-icon fas fa-user cyan"></i>
                                        <p>Utilisateurs</p>
                                    </router-link>
                                </li>
                            @endcan
                                
                                <li class="nav-item">
                                    <router-link to="/Fournisseurs" class="nav-link">
                                        <i class="nav-icon fas fa-industry blue"></i>
                                        <p>Fournisseurs</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/Machines" class="nav-link">
                                        <i class="nav-icon fas fa-robot indigo"></i>
                                        <p>Machines</p>
                                    </router-link>
                                </li>             
                            </ul>
                        </li>
                        
                    @endif

                        <li class="nav-item">
                            <router-link to="/Preventions" class="nav-link">
                            <i class="nav-icon fas fa-exclamation-triangle yellow"></i>
                                <p>
                                Interventions Préventives 
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/Corrections" class="nav-link">
                            <i class="nav-icon fas fa-tools orange"></i>
                                <p>
                                Interventions Correctives
                                </p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/Planning" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt green"></i><p>Planning</p>
                            </router-link>
                        </li>


                        
                        <li class="nav-item">
                            <router-link to="/Statistiques" class="nav-link">
                                <i class="nav-icon fas fa-chart-pie purple"></i>
                                <p>
                                Statistiques
                                </p>
                            </router-link>
                        </li>
                        


                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-power-off red"></i>
                                    <p>
                                    {{ __('Logout') }}
                                    </p>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
                <!--------------------------------- /.sidebar-menu ---------------------------------->
            </div>
            <!-------------------------------------- /.sidebar -------------------------------------->
        </aside>

            <!-------------------------------------- Page content ----------------------------------->
            <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid">
                    <router-view></router-view>
                    <!-- set progressbar -->
                    <vue-progress-bar></vue-progress-bar>
                </div>
            </div>
            </div>
        </div>
        <!-------------------------------------- ./wrapper -------------------------------------->
        
        @auth
        <script>
            window.user = @json(auth()->user())
        </script>
        @endauth

        @vite(['resources/js/app.js'])

    </body>
</html>
