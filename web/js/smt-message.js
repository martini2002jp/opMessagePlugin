'use strict';
$(document).ready(function() {

  var message = {

    /**
     * message template.
     */
    $template: $('#message-template'),

    /**
     * config.
     */
    config: {

      /**
       * hearbeat timer.
       */
      timer: null,

      /**
       * interval (second).
       */
      interval: 5
    },

    /**
     * initialize.
     */
    initialize: function() {
      // common.
      $('.message-created-at').timeago();

      // for message/smtChain page.
      if ($('body').is('#page_message_smtChain')) {
        // message date line. - show or hide.
        this.updateTimeInfo();

        this.addNewMessages(true).always(function() {
          // set timer.
          message.startHeartbeatTimer();
        });

        $('#do-submit').click(function() {
          message.clickDoSubmitButton();
        });

        $('#more').click(function() {
          message.clickMoreButton();
        });

        $('#message_image').change(function() {
          message.imageChangeValidator.call(this);
        });
      }
    },

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
     * hide more info fields.
     */
    hideMore: function() {
      $('#loading-more').hide();
      $('#more').hide();
    },

    /**
     * show more button.
     */
    showMore: function() {
      $('#loading-more').hide();
      $('#more').show();
    },

    /**
     * click #do-submit id button.
     */
    clickDoSubmitButton: function() {

      var body = $('#submit-message').val();
      if (1 > jQuery.trim(body)) {
        return;
      }

      var
        form = $('form#send-message-form'),
        formData = this.getFormData(form);

      this.submitFilter();
      this.stopHeartbeatTimer();

      $.ajax({
        url: openpne.apiBase + "message/post.json",
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        dataType: 'json',
      }).always(function() {

        message.addNewMessages(true).always(function() {
          message.updateTimeInfo();
          $('#no-message').hide();
          $('#submit-message').val('');
          $('#message_image').val('');
          message.submitFilter();
          message.startHeartbeatTimer();
        });
      });
    },

    /**
     * click #more id button.
     */
    clickMoreButton: function() {

      var
        firstMessageWrapper = $('.message-wrapper:first'),
        maxId = Number(firstMessageWrapper.attr('data-message-id'));

      if (isNaN(maxId))
      {
        return false;
      }

      this.moreFilter();

      this.getMessages(maxId, false).done(function(response) {

        message.insertMessages(response.data, false);

        message.moreFilter();

        if (!response.has_more)
        {
          message.hideMore();
        }

      }).fail(function() {
        // TODO error design.
      });
    },

    /**
     * check image file name if change image data.
     */
    imageChangeValidator: function() {

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
    },

    /**
     * get FromData Object. openpne apyKey and form value.
     */
    getFormData: function(form)
    {
      var
        formData = new FormData(form[0]);

      formData.append('apiKey', openpne.apiKey);
      formData.append('toMember', this.getMemberId());

      $(form.serializeArray()).each(function(i, v) {
        formData.append(v.name, v.value);
      });

      return formData;
    },

    /**
     * partner member id.
     */
    getMemberId: function() {
      var toMemberObj = $('#messageToMember');
      if (toMemberObj)
      {
        return toMemberObj.val();
      }

      return null;
    },

    /**
     * insert Message template by datas.
     * @param datas
     * @param isAddLow
     */
    insertMessages: function(datas, isAddLow) {

      if (!datas.length)
      {
        return false;
      }

      for (var i = 0; i < datas.length; i++)
      {
        this.insertMessageTemplate(datas[i], isAddLow);
      }

      this.updateTimeInfo();
    },

    /**
     * insert Message template by data.
     * @param data
     * @param isAddRow
     */
    insertMessageTemplate: function(data, isAddRow) {
      var
        template = this.$template.children().clone(),
        $photo = template.find('.photo'),
        position = data.member.id == this.getMemberId() ? 'right' : 'left';

      template
        .attr('data-message-id', data.id)
        .addClass(position)
        .addClass('show')
          .find('.time-info')
          .append(data.formatted_date)
            .parent('.time-info-wrapper')
            .attr('data-created-at-date', data.formatted_date)
          .end()
        .end()
          .find('.popover-title')
          .append(data.member.name)
        .end()
          .find('.message-body')
          .append(data.body)
        .end()
          .find('.message-created-at')
          .addClass(position)
          .attr('title', data.created_at)
          .timeago()
        .end();

      // has one image data from api. this opMessagePlugin version.
      if (data.image_path && data.image_tag) {
        $photo.append('<li><a href="' + data.image_path + '">' + data.image_tag + '</a></li>');
      } else {
        $photo.remove();
      }

      if (isAddRow) {
        $('#message-wrapper-parent').append(template);
      } else {
        $('#message-wrapper-parent').prepend(template);
      }
    },

    /**
     * update Time info line.
     */
    updateTimeInfo: function() {
      var
        timeInfoWrapper = $('#message-wrapper-parent').find('.time-info-wrapper'),
        currentDate,
        baseDate;

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
    },

    /**
     * start heartbeat timer.
     */
    startHeartbeatTimer: function() {
      this.config.timer = setTimeout(this.addNewMessages.bind(false), this.config.interval * 1000);
    },

    /**
     * stop heartbeat timer.
     */
    stopHeartbeatTimer: function() {
      clearTimeout(this.config.timer);
    },

    /**
     * insert Message template by data.
     * @param keyId
     * @param isAddLow
     */
    getMessages: function(keyId, isAddLow) {

      var dfd = $.Deferred();

      $.ajax({
        url: openpne.apiBase + "message/search.json",
        type: 'POST',
        data: {
          apiKey: openpne.apiKey,
          memberId: Number(this.getMemberId()),
          maxId: Number(keyId),
          isAddLow: Number(isAddLow)
        },
        dataType: 'json',
        success: function(response) {
          dfd.resolve(response);
        },
        error: function(e) {
          dfd.reject();
        }
      });

      return dfd.promise();
    },

    /**
     * add new messages.
     */
    addNewMessages: function(notUseHeartbeat) {

      var
        lastMessageWrapper = $('#message-wrapper-parent').find('.message-wrapper:last'),
        minId = Number(lastMessageWrapper.attr('data-message-id')),
        dfd = $.Deferred();

      if (isNaN(minId))
      {
        minId = -1;
      }

      message.getMessages(minId, true).done(function(response) {

        $('#first-loading').hide();
        message.insertMessages(response.data, true);
        dfd.resolve();

        if (!$('#message-wrapper-parent').find('.message-wrapper').length) {
          message.hideMore();
          $('#no-message').show();

          return false;
        }

        $('#no-message').hide();

        if (minId == -1 && response.has_more) {
          message.showMore();
        }

      }).always(function() {

        if (!notUseHeartbeat)
        {
          message.startHeartbeatTimer();
        }

      }).fail(function() {
        dfd.reject();
        // TODO error design.
      });

      return dfd.promise();
    }
  };

  message.initialize();
});
