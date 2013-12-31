'use strict';
$(document).ready(function() {

  var message = new Object();

  /**
   * message wrapper.
   */
  message.$messageWrapper = $('#message-wrapper-parent');

  /**
   * message template.
   */
  message.$template = $('#message-template');

  /**
   * hide and show submit fields.
   */
  message.submitFilter = function() {
    $('#loading').toggle();
    $('#submit-message').toggle();
    $('#do-submit').toggle();
    $('#message_image').toggle();
  };

  /**
   * hide and show more info fields.
   */
  message.moreFilter = function() {
    $('#loading-more').toggle();
    $('#more').toggle();
  };

  /**
   * click #do-submit id button.
   */
  message.clickDoSubmitButton = function() {

    var body = $('#submit-message').val();
    if (1 > jQuery.trim(body))
    {
      return;
    }
    this.submitFilter();

    var
      form = $('form#send-message-form'),
      formData = this.getFormData(form);

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
          message.insertMessageTemplate(res.data, true);
          message.updateTimeInfo();

          $('#no-message').hide();
        }
      },
      error: function(e) {
        console.log(e);
      },
      complete: function() {
        $('#submit-message').val('');
        $('#message_image').val('');
        message.submitFilter();
      }
    });
  };

  /**
   * click #more id button.
   */
  message.clickMoreButton = function() {

    var
      firstMessageWrapper = $('.message-wrapper:first'),
      maxId = -1;

    this.moreFilter();

    if (firstMessageWrapper)
    {
      maxId = parseInt(firstMessageWrapper.attr('data-message-id'));
    }

    $.ajax({
      url: openpne.apiBase + "message/search.json",
      type: 'GET',
      data: {
        apiKey: openpne.apiKey,
        memberId: this.getMemberId(),
        maxId: maxId
      },
      dataType: 'json',
      success: function(res) {
        if (0 < res.data.length)
        {
          for (var i = 0; i < res.data.length; i++)
          {
            message.insertMessageTemplate(res.data[i], false);
          }

          message.updateTimeInfo();

          message.moreFilter();
        }

        if (!res.has_more)
        {
          $('#more').hide();
          $('#loading-more').hide();
        }
      },
      error: function(e) {
        console.log(e);
      }
    });
  };

  /**
   * check image file name if change image data.
   */
  message.imageChangeValidator = function() {

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
  };

  /**
   * get FromData Object. openpne apyKey and form value.
   */
  message.getFormData = function(form)
  {
    var
      formData = new FormData(form[0]);

    formData.append('apiKey', openpne.apiKey);
    formData.append('toMember', this.getMemberId());

    $(form.serializeArray()).each(function(i, v) {
      formData.append(v.name, v.value);
    });

    return formData;
  };

  /**
   * partner member id.
   */
  message.getMemberId = function() {
    var toMemberObj = $('#messageToMember');
    if (toMemberObj)
    {
      return toMemberObj.val();
    }

    return null;
  };

  /**
   * insert Message template by data.
   * @param data
   * @param isAddRow
   */
  message.insertMessageTemplate = function(data, isAddRow) {
    var
      template = this.$template.children().clone(),
      $timeInfo = template.find('.time-info'),
      $timeInfoWrapper = $timeInfo.parent('.time-info-wrapper'),
      $popoverTitle = template.find('.popover-title'),
      $messageBody = template.find('.message-body'),
      $photo = template.find('.photo'),
      $messageCreatedAt = template.find('.message-created-at'),
      position = data.member.id == this.getMemberId() ? 'right' : 'left';

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

    isAddRow ? this.$messageWrapper.append(template) : this.$messageWrapper.prepend(template);
  };

  /**
   * update Time info line.
   */
  message.updateTimeInfo = function() {
    var
      timeInfoWrapper = this.$messageWrapper.find('.time-info-wrapper'),
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
  };

  message.updateTimeInfo();

  $('.message-created-at').timeago();

  $('#do-submit').click(function() {
    message.clickDoSubmitButton();
  });

  $('#more').click(function() {
    message.clickMoreButton();
  });

  $('#message_image').change(function() {
    message.imageChangeValidator.call(this);
  });
});
