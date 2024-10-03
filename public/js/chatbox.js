Pusher.logToConsole = true;
var pusher = new Pusher('f833ed006b07e2964bfd', {
  cluster: 'ap1'
});
// Hàm đóng hộp thoại chat
function closeChatBox() {
  const chatBox = document.getElementById('chatBox');
  chatBox.classList.remove('show');
}
function formatTime(timestamp) {
  const now = new Date();
  const messageTime = new Date(timestamp);
  const diffInSeconds = Math.floor((now - messageTime) / 1000); // Tính sự chênh lệch thời gian theo giây
  if (diffInSeconds < 60) {
    return 'Vài giây trước';
  } else if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60);
    return `${minutes} phút trước`;
  } else if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600);
    return `${hours} giờ trước`;
  } else if (diffInSeconds < 172800) { // Nếu dưới 2 ngày (86400 * 2 giây)
    return `Hôm qua lúc ${messageTime.getHours()}:${messageTime.getMinutes().toString().padStart(2, '0')}`;
  } else {
    const day = messageTime.getDate();
    const month = messageTime.getMonth() + 1; // Tháng bắt đầu từ 0 nên cần +1
    const year = messageTime.getFullYear();
    return `${day}/${month}/${year} lúc ${messageTime.getHours()}:${messageTime.getMinutes().toString().padStart(2, '0')}`;
  }
}
function MessageReceiver(request_id, receiver_id) {
  if (document.getElementById("message-`" + `${request_id}` + "`")) {
    document.getElementById("message-`" + `${request_id}` + "`").classList.remove('notification-badge');
  }
  var user = document.getElementById('user_curent');
  const chat = document.getElementById('chatBox');
  chat.classList.add('show');
  axios.get(`/dashboard/get/${request_id}/${receiver_id}`)
    .then(response => {
      const messages = response.data['messages'];
      const chatBox = document.getElementById('chat-box');
      chatBox.innerHTML = '';
      document.getElementById('receiverName').textContent = response.data['user_name'].username;
      const requestIdInput = document.createElement('input');
      requestIdInput.setAttribute('type', 'hidden');
      requestIdInput.setAttribute('id', 'request_id');
      requestIdInput.setAttribute('value', request_id);
      const receiverIdInput = document.createElement('input');
      receiverIdInput.setAttribute('type', 'hidden');
      receiverIdInput.setAttribute('id', 'receiver_id');
      receiverIdInput.setAttribute('value', receiver_id);
      // Tạo input để nhập tin nhắn
      const messageInputGroup = document.createElement('div');
      messageInputGroup.classList.add('input-group');
      messageInputGroup.style.marginTop = '4px';
      const messageInput = document.createElement('input');
      messageInput.setAttribute('type', 'text');
      messageInput.setAttribute('id', 'message');
      messageInput.classList.add('form-control');
      messageInput.setAttribute('placeholder', 'Nhập tin nhắn...');
      messageInput.setAttribute('required', true);
      const sendButton = document.createElement('button');
      sendButton.classList.add('btn-primary');
      sendButton.setAttribute('type', 'submit');
      sendButton.textContent = 'Gửi';
      messageInputGroup.appendChild(messageInput);
      messageInputGroup.appendChild(sendButton);
      const chatForm = document.getElementById('chat-form');
      chatForm.innerHTML = '';
      chatForm.appendChild(requestIdInput);
      chatForm.appendChild(receiverIdInput);
      chatForm.appendChild(messageInputGroup);
      var settingsIcon = document.querySelector('#messageCount');
      let user, receiver_ids, count_is_seen = 0;
      messages.forEach((message) => {
        const messageElement = document.createElement('div');
        messageElement.className = 'message';
        messageElement.id = 'message-' + message.message_id;
        messageElement.setAttribute('data-message-id', message.message_id);
        messageElement.style.marginBottom = '10px';
        const messageTime = formatTime(message.created_at);
        if (message.sender_id == response.data['user']) {
          messageElement.classList.add('message-send');
          const timeElement = document.createElement('span');
          timeElement.classList.add('message-time');
          timeElement.textContent = messageTime;
          const textElement = document.createElement('span');
          textElement.textContent = message.message;
          messageElement.appendChild(timeElement);
          messageElement.appendChild(textElement);
          chatBox.appendChild(messageElement);
        } else {
          messageElement.classList.add('message-received');
          const profileImgContainer = document.createElement('div');
          profileImgContainer.classList.add('message-profile-img-container');
          const profileImg = document.createElement('img');
          profileImg.classList.add('message-profile-img', 'avatar', 'me-3');
          profileImg.src = '../assets/img/team-2.jpg';
          profileImg.alt = "Receiver Image";
          profileImgContainer.appendChild(profileImg);
          const messageContent = document.createElement('div');
          messageContent.classList.add('message-content');
          const timeElement = document.createElement('span');
          timeElement.classList.add('message-time');
          timeElement.textContent = messageTime;
          const textElement = document.createElement('span');
          textElement.textContent = message.message;
          messageContent.appendChild(timeElement);
          messageContent.appendChild(textElement);
          messageElement.appendChild(profileImgContainer);
          messageElement.appendChild(messageContent);
          chatBox.appendChild(messageElement);
        }
        user = response.data['user'];
        receiver_ids = message.receiver_id;
        if (message.is_seen == false && message.receiver_id == response.data['user']) {
          count_is_seen++;
        }
        if (!message.is_seen && message.receiver_id == response.data['user']) {
          axios.get(`/dashboard/seen/${message.message_id}`)
            .then(response => {
            })
            .catch(error => {
              console.error(error);
            });
        }
      });
      const lastMessage = chatBox.lastElementChild;
      if (lastMessage) {
        if (receiver_ids != user) {
          const sentStatus = document.createElement('span');
          sentStatus.classList.add('message-status');
          sentStatus.textContent = 'Đã gửi';
          chatBox.appendChild(sentStatus);
          document.getElementById('seenImage').style.display = 'none'
        } else {
          document.getElementById('seenImage').style.display = 'block';
        }
      }
      if (settingsIcon.textContent != 0) {
        settingsIcon.textContent = Number(settingsIcon.textContent) - count_is_seen;
      }
      chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(error => {
      console.log(error);
    });
}
if (document.getElementById('chat-form')) {
  document.getElementById('chat-form').addEventListener('submit', function (e) {
    e.preventDefault();
    let request_id = document.getElementById('request_id').value;
    let receiver_id = document.getElementById('receiver_id').value;
    let message = document.getElementById('message').value;
    axios.post(`/dashboard/send/${request_id}/${receiver_id}/${message}`)
      .then(response => {
        document.getElementById('message').value = ''; // Xóa nội dung ô nhập
      })
      .catch(error => {
        console.log(error);
      });
  });
}
if (!channel) {
  var channel = pusher.subscribe('my-channel');
  channel.bind('my-event', function (data) {
    var user = document.getElementById('user_curent');
    if (Number(user.textContent) != data.message.sender_id) {
      MessageReceiver(data.message.request_id, data.message.sender_id);
    } else {
      MessageReceiver(data.message.request_id, data.message.receiver_id);
    }
  });
}
dayjs.extend(window.dayjs_plugin_relativeTime);
var channel = pusher.subscribe('people-message');
channel.bind('my-event-people', function (data) {
  const results = data.results;
  const messages = data.message;
  const messageCount = data.count;
  var results_c, message_c, messageCount_c;
  var user = document.getElementById('user_curent');
  if (Number(user.textContent) != results[0].user_c) {
    axios.get(`/dashboard/usercurrent`)
      .then(response => {
        results_c = response.data['results'];
        message_c = response.data['message'];
        messageCount_c = response.data['count'];
        document.getElementById('messageCount').textContent = messageCount_c;
        const settingsDropdown = document.getElementById('settings');
        settingsDropdown.innerHTML = '';
        if (results_c.length > 0) {
          results_c.forEach(function (result) {
            const listItem = document.createElement('li');
            listItem.classList.add('mb-2');
            const linkItem = document.createElement('a');
            linkItem.classList.add('dropdown-item', 'border-radius-md');
            linkItem.setAttribute('href', 'javascript:;');
            linkItem.onclick = function () {
              MessageReceiver(result.request_id, result.receiver_id);
            };
            const divContainer = document.createElement('div');
            divContainer.classList.add('d-flex', 'py-1');
            const imgDiv = document.createElement('div');
            imgDiv.classList.add('my-auto');
            const img = document.createElement('img');
            img.setAttribute('src', '../assets/img/team-2.jpg');
            img.classList.add('avatar', 'avatar-sm', 'me-3');
            imgDiv.appendChild(img);
            const infoDiv = document.createElement('div');
            infoDiv.classList.add('d-flex', 'flex-column', 'justify-content-center');
            const title = document.createElement('h6');
            title.classList.add('text-sm', 'font-weight-normal', 'mb-1');
            title.innerHTML = `<span class="font-weight-bold">${result.receiver_name}</span>`;
            const messageParagraph = document.createElement('p');
            messageParagraph.classList.add('text-xs', 'text-secondary', 'mb-0');
            let messageContent = 'Giờ đây, các bạn có thể nhắn tin cho nhau.';
            let m = '';
            if (message_c.length > 0) {
              message_c.forEach(function (message) {
                if (message_c.length < results_c.length && message.request_id != result.request_id) {
                  messageParagraph.innerHTML = messageContent;
                }
                if (message.request_id == result.request_id) {
                  m = `${message.message}. ${formatTime(message.created_at)}`;
                  if (!message.is_seen && message.sender_id == result.receiver_id) {
                    m += '<span class="notification-badge" id="message-`' + `${message.request_id}` + '`"></span>';
                  }
                  messageParagraph.innerHTML = m;
                }
              });
            } else {
              messageParagraph.innerHTML = messageContent;
            }
            infoDiv.appendChild(title);
            infoDiv.appendChild(messageParagraph);
            divContainer.appendChild(imgDiv);
            divContainer.appendChild(infoDiv);
            linkItem.appendChild(divContainer);
            listItem.appendChild(linkItem);
            settingsDropdown.appendChild(listItem);

          });
        } else {
          // Trường hợp không có dữ liệu
          const listItem = document.createElement('li');
          listItem.classList.add('mb-2');
          const linkItem = document.createElement('a');
          linkItem.classList.add('dropdown-item', 'border-radius-md');
          linkItem.setAttribute('href', 'javascript:;');
          linkItem.innerHTML = '<div class="d-flex py-1"><span class="font-weight-bold">Không có dữ liệu</span></div>';
          listItem.appendChild(linkItem);
          settingsDropdown.appendChild(listItem);
        }
      })
      .catch(error => {
        console.error(error);
      });

  } else {
    // document.getElementById('messageCount').textContent = messageCount;
    const settingsDropdown = document.getElementById('settings');
    settingsDropdown.innerHTML = '';
    if (results.length > 0) {
      results.forEach(function (result) {
        const listItem = document.createElement('li');
        listItem.classList.add('mb-2');
        const linkItem = document.createElement('a');
        linkItem.classList.add('dropdown-item', 'border-radius-md');
        linkItem.setAttribute('href', 'javascript:;');
        linkItem.onclick = function () {
          MessageReceiver(result.request_id, result.receiver_id);
        };
        const divContainer = document.createElement('div');
        divContainer.classList.add('d-flex', 'py-1');
        const imgDiv = document.createElement('div');
        imgDiv.classList.add('my-auto');
        const img = document.createElement('img');
        img.setAttribute('src', '../assets/img/team-2.jpg');
        img.classList.add('avatar', 'avatar-sm', 'me-3');
        imgDiv.appendChild(img);
        const infoDiv = document.createElement('div');
        infoDiv.classList.add('d-flex', 'flex-column', 'justify-content-center');
        const title = document.createElement('h6');
        title.classList.add('text-sm', 'font-weight-normal', 'mb-1');
        title.innerHTML = `<span class="font-weight-bold">${result.receiver_name}</span>`;
        const messageParagraph = document.createElement('p');
        messageParagraph.classList.add('text-xs', 'text-secondary', 'mb-0');
        let messageContent = 'Giờ đây, các bạn có thể nhắn tin cho nhau.';
        let m = '';
        if (data.message.length > 0) {
          messages.forEach(function (message) {
            if (data.message.length < data.results.length && message.request_id != result.request_id) {
              messageParagraph.innerHTML = messageContent;
            }
            if (message.request_id == result.request_id) {
              m = `${message.message}. ${formatTime(message.created_at)}`;
              if (!message.is_seen && Number(user.textContent) == result.receiver_id) {
                m += '<span class="notification-badge" id="message-`' + `${message.request_id}` + '`"></span>';
              }
              messageParagraph.innerHTML = m;
            }
          });

        } else {
          messageParagraph.innerHTML = messageContent;
        }
        infoDiv.appendChild(title);
        infoDiv.appendChild(messageParagraph);
        divContainer.appendChild(imgDiv);
        divContainer.appendChild(infoDiv);
        linkItem.appendChild(divContainer);
        listItem.appendChild(linkItem);
        settingsDropdown.appendChild(listItem);
      });
    } else {
      const listItem = document.createElement('li');
      listItem.classList.add('mb-2');
      const linkItem = document.createElement('a');
      linkItem.classList.add('dropdown-item', 'border-radius-md');
      linkItem.setAttribute('href', 'javascript:;');
      linkItem.innerHTML = '<div class="d-flex py-1"><span class="font-weight-bold">Không có dữ liệu</span></div>';
      listItem.appendChild(linkItem);
      settingsDropdown.appendChild(listItem);
    }
  }
});
function test(request_id, status) {
  axios.get(`/dashboard/update/${request_id}/${status}`)
    .then(response => {
    })
    .catch(error => {
      console.log(error);
    });
}
function Notification(notification) {
  var notification_id = Number(notification);
  var settingsIcon = document.querySelector('#notificationCount');
  settingsIcon.textContent = Number(settingsIcon.textContent) - 1;
  axios.get(`/dashboard/read/${notification_id}`)
    .then(response => {
      document.getElementById("notification-`" + `${notification_id}` + "`").classList.remove('notification-badge');
    })
    .catch(error => {
      console.log(error);
    });
}
if (!channelnotification) {
  var channelnotification = pusher.subscribe('channel-request');
  channelnotification.bind('my-event-request', function (data) {
    if (document.getElementById('remove-notification')) {
      document.getElementById('remove-notification').innerHTML = '';
    }
    var notificationDropdown = document.getElementById('notifications');
    var settingsIcon = document.querySelector('#notificationCount');
    var count = 0;
    var user = document.getElementById('user_curent');
    if (Number(user.textContent) != data.user.user_id) {
      var notification = (Number(user.textContent) == data.notification_c.user_id) ? data.notification_c : data.notification_t;
      if (!notification.is_read) {
        count++;
      }
      settingsIcon.textContent = Number(settingsIcon.textContent) + count;
      var listItem = document.createElement('li');
      listItem.className = 'mb-2';
      var anchor = document.createElement('a');
      anchor.className = 'dropdown-item border-radius-md';
      anchor.href = "javascript:;";
      anchor.addEventListener('click', function () {
        Notification(notification.notification_id);
      });
      var div = document.createElement('div');
      div.className = 'd-flex py-1';
      var imgDiv = document.createElement('div');
      imgDiv.className = 'my-auto';
      var img = document.createElement('img');
      img.src = "../assets/img/team-2.jpg";
      img.className = "avatar avatar-sm me-3";
      imgDiv.appendChild(img);
      var textDiv = document.createElement('div');
      textDiv.className = 'd-flex flex-column justify-content-center';
      var h6 = document.createElement('h6');
      h6.className = 'text-sm font-weight-normal mb-1';
      h6.innerHTML = `<span class="font-weight-bold">${notification.message}</span>`;
      var p = document.createElement('p');
      p.className = 'text-xs text-secondary mb-0';
      p.innerHTML = `<i class="fa fa-clock me-1"></i> ${formatTime(notification.created_at)}`;
      textDiv.appendChild(h6);
      textDiv.appendChild(p);
      div.appendChild(imgDiv);
      div.appendChild(textDiv);
      anchor.appendChild(div);
      listItem.appendChild(anchor);
      if (notificationDropdown.firstChild) {
        notificationDropdown.insertBefore(listItem, notificationDropdown.firstChild);
      } else {
        notificationDropdown.appendChild(listItem);
      }
      if (!notification.is_read) {
        var badge = document.createElement('span');
        badge.className = 'notification-badge';
        badge.id = 'notification-`' + `${notification.notification_id}` + '`';
        listItem.appendChild(badge);
      }
    } else {
      var notification = (data.user.user_id == data.notification_c.user_id) ? data.notification_c : data.notification_t;
      if (!notification.is_read) {
        count++;
      }
      settingsIcon.textContent = Number(settingsIcon.textContent) + count;
      var listItem = document.createElement('li');
      listItem.className = 'mb-2';
      var anchor = document.createElement('a');
      anchor.className = 'dropdown-item border-radius-md';
      anchor.href = "javascript:;";
      anchor.addEventListener('click', function () {
        Notification(notification.notification_id);
      });
      var div = document.createElement('div');
      div.className = 'd-flex py-1';
      var imgDiv = document.createElement('div');
      imgDiv.className = 'my-auto';
      var img = document.createElement('img');
      img.src = "../assets/img/team-2.jpg";
      img.className = "avatar avatar-sm me-3";
      imgDiv.appendChild(img);
      var textDiv = document.createElement('div');
      textDiv.className = 'd-flex flex-column justify-content-center';
      var h6 = document.createElement('h6');
      h6.className = 'text-sm font-weight-normal mb-1';
      h6.innerHTML = `<span class="font-weight-bold">${notification.message}</span>`;
      var p = document.createElement('p');
      p.className = 'text-xs text-secondary mb-0';
      p.innerHTML = `<i class="fa fa-clock me-1"></i> ${formatTime(notification.created_at)}`;
      textDiv.appendChild(h6);
      textDiv.appendChild(p);
      div.appendChild(imgDiv);
      div.appendChild(textDiv);
      anchor.appendChild(div);
      listItem.appendChild(anchor);
      if (notificationDropdown.firstChild) {
        notificationDropdown.insertBefore(listItem, notificationDropdown.firstChild);
      } else {
        notificationDropdown.appendChild(listItem);
      }
      if (!notification.is_read) {
        var badge = document.createElement('span');
        badge.className = 'notification-badge';
        badge.id = 'notification-`' + `${notification.notification_id}` + '`';
        listItem.appendChild(badge);
      }
    }
  });

}




