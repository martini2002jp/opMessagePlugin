$(function() {
  $('.message-created-at').timeago();

  $('#do-submit').click(function() {
    var body = $('#submit-message').val();
    if (1 > jQuery.trim(body))
    {
      return;
    }
    submitFilter();

    var form = $('form');
    var fd = new FormData(form[0]);
    var json = getParams();

    for (i in json)
    {
      fd.append(i, json[i]);
    }

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
          $('#message-wrapper-parent').append(addTemplate(res.data, memberId));

          $('#no-message').hide();
        }
      },
      error: function(e) {
        console.log(e);
      },
      complete: function() {
        $('#submit-message').val('');
        $('#message_image').val('');
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
            $('#message-wrapper-parent').prepend(addTemplate(res.data[i], memberId));
          }

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

  $('#message_image').change(function() {
    var a = $(this).prop('files');
    if(0 < a.length)
    {
      var fileType = a[0].type;
      if (null === fileType.match(/(jpeg|gif|png)/))
      {
        alert('ファイル形式が間違っています。');
        $(this).val('');
      }
    }
  });
});

function submitFilter()
{
  $('#loading').toggle();
  $('#submit-message').toggle();
  $('#do-submit').toggle();
  $('#message_image').toggle();
}

function moreFilter()
{
  $('#loading-more').toggle();
  $('#more').toggle();
}

function getParams()
{
  var query = $('form').serializeArray(),
    json = {apiKey: openpne.apiKey};
  for (i in query)
  {
     json[query[i].name] = query[i].value
  }

  json['toMember'] = $('#do-submit').attr('to-member');

  return json;
}

function addTemplate(data, memberId)
{
  var template = $('#message-template').children().clone();
  template.find('.member-image').attr('src', data.member.profile_image);
  template.find('.popover-title').append(data.member.name);
  template.find('.message-body').append(data.body);
  template.attr('data-message-id', data.id);
  if (data.image_path && data.image_tag)
  {
    template.find('.photo').append('<li><a href="' + data.image_path + '">' + data.image_tag + '</a></li>');
  }
  else
  {
    template.find('.photo').remove();
  }
  var position = data.member.id == memberId ? 'right' : 'left';
  template.find('.message-created-at').addClass(position).attr('title', data.created_at).timeago();
  template.addClass(position);
  template.addClass('show');

  return template;
}
