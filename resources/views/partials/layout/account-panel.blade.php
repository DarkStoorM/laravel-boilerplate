<div class="navbar">
    @auth
        <div>
            <a href="{{ route(NamedRoute::GET_DASHBOARD_INDEX) }}" dusk="link-dashboard">@lang("links.index.dashboard")</a>
        </div>
        <div>
            <a href="{{ route(NamedRoute::GET_SESSION_DESTROY) }}" dusk="link-logout">@lang('links.index.logout')</a>
        </div>
    @endauth

    @guest
        <div>
            <a href="{{ route(NamedRoute::GET_SESSION_INDEX) }}" dusk="link-login">@lang('links.index.login')</a>
        </div>
        <div>
            <a href="{{ route(NamedRoute::GET_ACCOUNT_CREATION_INDEX) }}" dusk="link-signup">@lang('links.index.signup')</a>
        </div>
    @endguest
</div>
