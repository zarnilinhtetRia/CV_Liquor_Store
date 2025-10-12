<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <!-- <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>


    </ul> -->
    <!-- <ul class="navbar-nav ml-auto">


        <li class="nav-item">


            <a href="{{ url('/logout') }}" class="btn btn-primary">Logout</a>


        </li>
    </ul> -->
    <ul class="navbar-nav col-md-6">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white" href="#">Date -
                <?= $currentDate = date('d-m-y') ?></a>
        </li>


    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">


        <div class="btn-group">
            <button type="button" class="btn text-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ auth()->user()->name }}
            </button>
            <div class="dropdown-menu ">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn  p-1 changelogout " style="width: 157px">
                        <i class="fa-solid fa-right-from-bracket "></i> Logout</button>

                </form>


            </div>
        </div>

        {{-- <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Logout</button>
        </form> --}}

    </ul>
</nav>
