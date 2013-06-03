$(function() {
  jQuery(".timeago").timeago();

  $('#submit').click(function() {
    var body = $('#submit-message').val();
    if (1 > jQuery.trim(body))
    {
      return;
    }
    submitFilter();

    var form = $('form');
    var fd = new FormData(form[0]);
    fd.append('body', body);
    fd.append('toMember', $(this).attr('to-member'));
    fd.append('apiKey', openpne.apiKey);
    // TODO 画像投稿出来るように

    $.ajax({
      url: openpne.apiBase + "message/post.json",
      type: 'POST',
      processData: false,
      contentType: false,
      data: fd,
      dataType: 'json',
      success: function(res) {
        if ('success' === res.status)
        {
          var template = $('#message-template').clone();
          template.find('.member-link').attr('href', res.data.member.profile_url);
          template.find('.member-image').attr('src', res.data.member.profile_image);
          template.find('.member-name').append(res.data.member.name);
          template.find('.message-body').append(res.data.body);
          template.find('.message-created-at').attr('title', res.data.created_at);
          template.removeAttr('id');
          template.css('display', 'block');
          template.css('margin-bottom', '30px'); $('#message-wrapper-parent').append(template);

          jQuery(".timeago").timeago();
          $('#no-message').hide();
        }
      },
      error: function(e) {
        console.log(e);
      },
      complete: function() {
        $('#submit-message').val('');
        submitFilter();
      }
    });
  });

  $('#more').click(function() {
    moreFilter();

    var maxId = -1;
    $('.message-wrapper').each(function() {
      if (0 > maxId || maxId > parseInt($(this).attr('data-message-id')))
      {
        maxId = parseInt($(this).attr('data-message-id'));
      }
    });

    $.ajax({
      url: openpne.apiBase + "message/search.json",
      type: 'GET',
      data: {
        apiKey: openpne.apiKey,
        memberId: memberId,
        maxId: maxId
      },
      dataType: 'json',
      success: function(res) {
        if (0 < res.data.length)
        {
          for (var i = 0; i < res.data.length; i++)
          {
            var template = $('#message-template').clone();
            template.find('.member-link').attr('href', res.data[i].member.profile_url);
            template.find('.member-image').attr('src', res.data[i].member.profile_image);
            template.find('.member-name').append(res.data[i].member.name);
            template.find('.message-body').append(res.data[i].body);
            template.find('.message-created-at').attr('title', res.data[i].created_at);
            template.removeAttr('id');
            template.attr('data-message-id', res.data[i].id);
            template.css('display', 'block');
            template.css('margin-bottom', '30px');
            $('#message-wrapper-parent').prepend(template);
          }

          jQuery(".timeago").timeago();
          moreFilter();
        }

        if (25 > res.data.length)
        {
          $('#more').hide();
          $('#loading-more').hide();
        }
      },
      error: function(e) {
        console.log(e);
      }
    });
  });
});

function submitFilter()
{
  $('#loading').toggle();
  $('#submit-message').toggle();
  $('#submit').toggle();
}

function moreFilter()
{
  $('#loading-more').toggle();
  $('#more').toggle();
}
