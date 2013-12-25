'use strict';
$(document).ready(function() {

  var
   /**
    * message wrapper.
    */
    $messageWrapper = $('#message-wrapper-parent'),

   /**
    * message functions.
    */
    func = {

      /**
       * hide and show submit fields.
       */
      submitFilter: function() {
        $('#loading').toggle();
        $('#submit-message').toggle();
        $('#do-submit').toggle();
        $('#message_image').toggle();
      },

      /**
       * hide and show more info fields.
       */
      moreFilter: function() {
        $('#loading-more').toggle();
        $('#more').toggle();
      },

      /**
       * get FromData Object. openpne apyKey and form value.
       */
      getFormData: function(form)
      {
        var
          formData = new FormData(form[0]);

        formData.append('apiKey', openpne.apiKey);

        $(form.serializeArray()).each(function(i, v) {
          formData.append(v.name, v.value);
        });

        return formData;
      }
    };

  $messageWrapper.param = {

   /**
    * partner member id.
    */
    memberId: memberId,

   /**
    * message template.
    */
    $template: $('#message-template'),
  };

  $messageWrapper.func = {

    /**
     * insert Message template by data.
     * @param data
     * @param isAddRow
     */
    insertMessageTemplate: function(data, isAddRow) {
      var
        template = $messageWrapper.param.$template.children().clone(),
        $timeInfo = template.find('.time-info'),
        $timeInfoWrapper = $timeInfo.parent('.time-info-wrapper'),
        $popoverTitle = template.find('.popover-title'),
        $messageBody = template.find('.message-body'),
        $photo = template.find('.photo'),
        $messageCreatedAt = template.find('.message-created-at'),
        position = data.member.id == this.memberId ? 'right' : 'left';

      template.attr('data-message-id', data.id);
      template.addClass(position);
      template.addClass('show');

      $timeInfo.append(data.formatted_date);
      $timeInfoWrapper.attr('data-created-at-date', data.formatted_date);

      $popoverTitle.append(data.member.name);
      $messageBody.append(data.body);

      // has one image data from api. this opMessagePlugin version.
      if (data.image_path && data.image_tag) {
        $photo.append('<li><a href="' + data.image_path + '">' + data.image_tag + '</a></li>');
      } else {
        $photo.remove();
      }

      $messageCreatedAt
        .addClass(position)
        .attr('title', data.created_at)
        .timeago();

      isAddRow ? $messageWrapper.append(template) : $messageWrapper.prepend(template);
    },

    /**
     * update Time info line.
     */
    updateTimeInfo: function() {
      var
        timeInfoWrapper = $messageWrapper.find('.time-info-wrapper'),
        currentDate,
        baseDate;

      if (timeInfoWrapper.length) {
        for (var i = 0; i < timeInfoWrapper.length; i++) {
          currentDate = timeInfoWrapper.eq(i).attr('data-created-at-date');
          if (currentDate) {
            if (currentDate === baseDate) {
              timeInfoWrapper.eq(i).hide();
            } else {
              timeInfoWrapper.eq(i).show();
            }

            baseDate = currentDate;
          }
        }
      }
    }
  };

  $messageWrapper.func.updateTimeInfo();
  $('.message-created-at').timeago();

  $('#do-submit').click(function() {
    var body = $('#submit-message').val();
    if (1 > jQuery.trim(body))
    {
      return;
    }
    func.submitFilter();

    var
      form = $('form#send-message-form'),
      formData = func.getFormData(form);

    $.ajax({
      url: openpne.apiBase + "message/post.json",
      type: 'POST',
      processData: false,
      contentType: false,
      data: formData,
      dataType: 'json',
      success: function(res) {
        if ('success' === res.status)
        {
          $messageWrapper.func.insertMessageTemplate(res.data, true);
          $messageWrapper.func.updateTimeInfo();

          $('#no-message').hide();
        }
      },
      error: function(e) {
        console.log(e);
      },
      complete: function() {
        $('#submit-message').val('');
        $('#message_image').val('');
        func.submitFilter();
      }
    });
  });

  $('#more').click(function() {
    var
      firstMessageWrapper = $('.message-wrapper:first'),
      maxId = -1;

    func.moreFilter();

    if (firstMessageWrapper)
    {
      maxId = parseInt(firstMessageWrapper.attr('data-message-id'));
    }

    $.ajax({
      url: openpne.apiBase + "message/search.json",
      type: 'GET',
      data: {
        apiKey: openpne.apiKey,
        memberId: $messageWrapper.param.memberId,
        maxId: maxId
      },
      dataType: 'json',
      success: function(res) {
        if (0 < res.data.length)
        {
          for (var i = 0; i < res.data.length; i++)
          {
            $messageWrapper.func.insertMessageTemplate(res.data[i], false);
          }

          $messageWrapper.func.updateTimeInfo();

          func.moreFilter();
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
