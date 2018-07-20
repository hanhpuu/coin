
<div class="collapse navbar-collapse" id="app-navbar-collapse">
	<!-- Left Side Of Navbar -->
	@if(Auth::user())
	<ul class="nav navbar-nav">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				Coins <span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="{{ route('coins.create')}}">Add new coin</a></li>
				<li><a href="{{ route('coins.index')}}">View all coins</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				Sources <span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="{{ route('sources.create')}}">Add new source</a></li>
				<li><a href="{{ route('sources.index')}}">View all sources</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				Currency pairs <span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="{{ route('currency_pairs.create')}}">Add a new currency pair</a></li>
				<li><a href="{{ route('currency_pairs.index')}}">View all currency pairs</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				Distinct pairs <span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="{{ route('distinct_pairs.create')}}">Add a new distinct pair</a></li>
				<li><a href="{{ route('distinct_pairs.index')}}">View all distinct pairs</a></li>
				<li><a href="/potential_group">View a potential group</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				Time <span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="/time">Enter time</a></li>
			</ul>

		</li>
	</ul>
	@endif

	<!-- Right Side Of Navbar -->
	<ul class="nav navbar-nav navbar-right">
		<!-- Authentication Links -->
		@if (Auth::guest())
		<li><a href="{{ route('login') }}">Login</a></li>
		<li><a href="{{ route('register') }}">Register</a></li>
		@else
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				{{ Auth::user()->name }} <span class="caret"></span>
			</a>

			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="{{ route('logout') }}"
					   onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
						Logout
					</a>

					<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
						{{ csrf_field() }}
					</form>
				</li>
			</ul>
		</li>
		@endif
	</ul>
</div>
