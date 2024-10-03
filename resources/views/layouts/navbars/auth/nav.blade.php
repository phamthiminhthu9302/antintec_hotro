<style>
    .message-received {
        display: flex;
        align-items: center;
        border-radius: 10px;
        position: relative;
        max-width: 70%;
        margin-right: auto;
        word-wrap: break-word;
        align-self: flex-start;
        text-align: left;
    }

    .message-profile-img {
        width: 20px;
        height: 20px;
        margin-right: 5px;
        z-index: 2;
        position: relative;
    }

    .message-content {
        background-color: #F0F2F5;
        border-radius: 10px;
        padding: 10px;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        font-size: 12px;
    }

    .message-send {
        background-color: #1877F2;
        color: white;
        padding: 7px;
        border-radius: 10px;
        text-align: right;
        max-width: 70%;
        word-wrap: break-word;
        display: flex;
        flex-direction: column;
        margin-left: auto;
        position: relative;
        font-size: 12px;
    }

    .message-time {
        font-size: 12px;
        margin-bottom: 5px;
        align-self: flex-end;
    }

    .message-status {
        font-size: 12px;
        color: #8392AB;
        margin-top: 5px;
        display: block;
        text-align: right;
    }

    .notification-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 10px;
        height: 10px;
        background-color: blue;
        border-radius: 50%;
    }

    .nav-link .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: red;
        color: white;
        padding: 0px 0px;
        border-radius: 60%;
        font-size: 12px;
        line-height: 1;
        min-width: 15px;
        text-align: center;
        z-index: 1;
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">{{ str_replace('-', ' ', Request::path()) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ str_replace('-', ' ', Request::path()) }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
            <div class="nav-item d-flex align-self-end">
                <a href="https://www.creative-tim.com/product/soft-ui-dashboard-laravel" target="_blank" class="btn btn-primary active mb-0 text-white" role="button" aria-pressed="true">
                    Download
                </a>
            </div>
            <div class="ms-md-3 pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" placeholder="Type here...">
                </div>
            </div>
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="profile" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-user me-sm-1"></i>
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end  px-2  me-sm-n4" aria-labelledby="dropdownMenuButton">
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="{{ url('user-profile/update') }}">
                                <div class="d-flex py-1">

                                    <img src="../assets/img/profile.png" class=" avatar-sm  me-3 ">

                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">Profile</span>
                                        </h6>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="{{ url('/logout')}}">
                                <div class="d-flex py-1">

                                    <img src="../assets/img/logout.png" class=" avatar-sm  me-3 ">

                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">Sign Out</span>
                                        </h6>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Settings Dropdown -->
                <li class="nav-item dropdown pe-2 d-flex align-items-center ">
                    <a href="javascript:;" class="nav-link text-body p-0 " id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-cog cursor-pointer"></i>
                        <span class="badge" id="messageCount">{{$count_message}}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 me-sm-n4" id="settings" aria-labelledby="settingsDropdown">

                        @if(count($results) > 0)
                        @for($i = 0; $i < count($results); $i++)
                            <li class="mb-2 ">
                            <a class="dropdown-item border-radius-md " onclick="MessageReceiver( `{{$results[$i]['request_id']}}` ,  `{{ $results[$i]['receiver_id']}}` )">
                                <div class="d-flex py-1 ">
                                    <div class="my-auto">
                                        <img src="../assets/img/team-2.jpg" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center ">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold " id="chat-receiver_name">{{ $results[$i]['receiver_name'] }}</span>
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">
                                            @if(count($messages)>0)
                                            @for($j=0; $j < count($messages); $j++)
                                                @if(count($messages) < count($results) && $messages[$j]['request_id']!=$results[$i]['request_id'])
                                                Giờ đây, các bạn có thể nhắn tin cho nhau.
                                                @endif
                                                @if ($messages[$j]['request_id']==$results[$i]['request_id'])
                                                {{ $messages[$j]['message'] }}.
                                                {{ \Carbon\Carbon::parse($messages[$j]['created_at'])->diffForHumans() }}

                                                @if ($messages[$j]['is_seen']==false && $messages[$j]['sender_id']==$results[$i]['receiver_id'])

                                                <span class="notification-badge" id="message-`{{$messages[$j]['request_id']}}`"></span>
                                                @else
                                                <span></span>
                                                @endif

                                                @endif
                                                @endfor
                                                @else
                                                Giờ đây, các bạn có thể nhắn tin cho nhau.
                                                @endif
                                        </p>
                                    </div>
                                </div>

                            </a>
                </li>
                @endfor
                @else
                <li class="mb-2">
                    <a class="dropdown-item border-radius-md" href="javascript:;">
                        <div class="d-flex py-1">
                            <span class="font-weight-bold">Không có dữ liệu</span>

                        </div>
                    </a>
                </li>
                @endif
            </ul>
            </li>
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="notification_dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell cursor-pointer"></i>
                    <span class="badge" id="notificationCount">{{$count_notification}}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" id="notifications" aria-labelledby="notificationDropdown">

                    @if(count($notification) > 0)
                    @for($i = 0; $i < count($notification); $i++)
                        <li class="mb-2">
                        <a class="dropdown-item border-radius-md" href="javascript:; " onclick="Notification(`{{$notification[$i]['notification_id']}}`)">
                            <div class="d-flex py-1">
                                <div class="my-auto">
                                    <img src="../assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-sm font-weight-normal mb-1">
                                        <span class="font-weight-bold">{{$notification[$i]['message']}}</span>@if ($notification[$i]['is_read']==false)

                                        <span class="notification-badge" id="notification-`{{$notification[$i]['notification_id']}}`"></span> @else
                                        <span></span>

                                        @endif
                                    </h6>
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="fa fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($notification[$i]['created_at'])->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </a>

            </li>
            @endfor
            @else
            <li class="mb-2" id="remove-notification">
                <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                        <span class="font-weight-bold" id="not-notification">Không có thông báo</span>

                    </div>
                </a>
            </li>
            @endif

            </ul>
            </li>
            <div id="user_curent" style="display: none;">{{$user}}</div>
        </div>
    </div>
</nav>
<div id="user_curent" style="display: none;">{{$user}}</div>
<div id="chatBox" class="chat-box">
    <div class="chat-header">
        <img src="../assets/img/team-2.jpg" class="avatar avatar-sm  me-3 " style="  margin-right: auto;    margin-left: 5px;" />
        <span id="receiverName"></span>

        <button class="close-btn" onclick="closeChatBox()">×</button>
    </div>
    <div id="chat-box" style="height: 300px;">
    </div>
    <img id="seenImage" src="../assets/img/team-2.jpg" class="seen-img avatar me-2" style="position: absolute; bottom: 50px; right: 20px; width: 20px; height: 20px; display: none;" />
    <form id="chat-form">
    </form>
</div>
<style>
    .chat-box {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 300px;
        height: 400px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        transition: transform 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        transform: translateY(100%);
    }

    .chat-box.show {
        display: block;
        transform: translateY(0);
        opacity: 1;
    }

    .chat-header {
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
        color: black;
        display: flex;
        align-items: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .close-btn {
        background: none;
        border: none;
        color: black;
        font-size: 30px;
        cursor: pointer;
        margin-left: auto;
    }

    #chat-box {
        padding: 10px;
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
        height: 250px;
        overflow-y: auto;
    }

    .seen-img.show {
        display: block;
    }
</style>
<!-- End Navbar -->
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>

<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="../js/chatbox.js"></script>