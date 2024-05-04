<div class="user-info">
    <div class="image">
        <img src="{{ \Auth::user()->getImageUrl(48,48) }}" width="48" height="48" alt="User"/>
    </div>
    <div class="info-container">
        <div class="name" data-toggle="dropdown" aria-haspopup="true"
             aria-expanded="false">{{ $_admin->getName() }}</div>
        <div class="email">{{ $_admin->email }}</div>
        <div class="btn-group user-helper-dropdown">
            <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
            <ul class="dropdown-menu pull-right">
                <li><a href="{{ route('admin.account.reset-password') }}"><i class="material-icons">person</i>Change Password</a></li>
                <li><a href="{{ route('admin.profile') }}"><i class="material-icons">person</i>Update Profile</a></li>
                {{--<li role="seperator" class="divider"></li>--}}
                {{--<li><a href="javascript:void(0);"><i class="material-icons">group</i>Followers</a></li>--}}
                {{--<li><a href="javascript:void(0);"><i class="material-icons">shopping_cart</i>Sales</a></li>--}}
                {{--<li><a href="javascript:void(0);"><i class="material-icons">favorite</i>Likes</a></li>--}}
                {{--<li role="seperator" class="divider"></li>--}}
                <li><a href="{{ route('admin.auth.logout') }}"><i class="material-icons">input</i>Sign Out</a></li>
            </ul>
        </div>
    </div>
</div>