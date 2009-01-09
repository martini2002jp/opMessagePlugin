<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * message actions.
 *
 * @package    OpenPNE
 * @subpackage message
 * @author     Maki TAKAHASHI <maki@jobweb.co.jp>
 */
class opMessagePluginMessageActions extends opMessagePluginActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex($request)
  {
    $this->forward('message', 'receiveList');
    
  }
  
 /**
  * Executes receiveList action
  *
  * @param sfRequest $request A request object
  */
  public function executeReceiveList($request)
  {
    $this->pager = MessageSendListPeer::getReceiveMessagePager($this->getUser()->getMemberId(),
                                                                      $request->getParameter('page'),
                                                                      sfConfig::get ('app_message_pagenateSize'));
    $this->messageList($request, 'MessageSendList', 'message/index');
  }
  
 /**
  * Executes sendList action
  *
  * @param sfRequest $request A request object
  */
  public function executeSendList($request)
  {
    $this->pager = SendMessageDataPeer::getSendMessagePager($this->getUser()->getMemberId(),
                                                                      $request->getParameter('page'),
                                                                      sfConfig::get ('app_message_pagenateSize'));
    $this->messageList($request, 'Message', 'message/sendList');
  }
  
 /**
  * Executes draftList action
  *
  * @param sfRequest $request A request object
  */
  public function executeDraftList($request)
  {
    $this->pager = SendMessageDataPeer::getDraftMessagePager($this->getUser()->getMemberId(),
                                                            $request->getParameter('page'),
                                                            sfConfig::get ('app_message_pagenateSize'));
    $this->messageList($request, 'Message', 'message/draftList');
  }

 /**
  * Executes dustList action
  *
  * @param sfRequest $request A request object
  */
  public function executeDustList($request)
  {
    $this->pager = DeletedMessagePeer::getDeletedMessagePager($this->getUser()->getMemberId(),
                                                            $request->getParameter('page'),
                                                            sfConfig::get ('app_message_pagenateSize'));
    $this->messageList($request, 'DeletedMessage', 'message/dustList');
  }
  
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow($request)
  {
    $this->message = SendMessageDataPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404unless($message = $this->isReadable($request->getParameter('type')));
    switch ($request->getParameter('type')) {
      case "receive":
        $this->deleteButton = '@deleteReceiveMessage?id='.$message->getId();
        break;
      case "send":
        $this->deleteButton = '@deleteSendMessage?id='.$this->message->getId();
        break;
      case "dust":
        $this->deleteButton = '@deleteDustMessage?id='.$message->getId();
        $this->deletedId = $message->getId();
    }
  }
  
 /**
  * Executes delete action
  *
  * @param sfRequest $request A request object
  */
  public function executeDelete($request)
  {
    switch ($request->getParameter('type')) {
      case "receiveList":
        $object_name = 'MessageSendList';
        break;
      case "sendList":
        $object_name = 'Message';
        break;
      case "dustList":
        $object_name = 'DeletedMessage';
        break;
    }
    if ($object_name) {
      DeletedMessagePeer::deleteMessage(sfContext::getInstance()->getUser()->getMemberId(),
                                      $request->getParameter('id'), 
                                      $object_name);
    }
    $this->redirect('message/'.$request->getParameter('type'));
  }
  
 /**
  * Executes restore action
  *
  * @param sfRequest $request A request object
  */
  public function executeRestore($request)
  {
    DeletedMessagePeer::restoreMessage($request->getParameter('id'));
    $this->redirect('message/dustList');
  }
  
 /**
  * Executes sendMessage action
  *
  * @param sfRequest $request A request object
  */
  public function executeSendToFriend($request)
  {
    if ($request->getParameter('message')) {
      $send_member_id = $request->getParameter('message[send_member_id]');
      $this->message = SendMessageDataPeer::retrieveByPk($request->getParameter('message[id]'));
      $this->forward404Unless($this->isDraftOwner());
    } else if ($request->getParameter('id')) {
      $send_member_id = $request->getParameter('id');
      $this->message = new SendMessageData();
    } else {
      $this->forward404();
    }
    sfConfig::set('sf_navi_type', 'friend');
    sfConfig::set('sf_navi_id', $send_member_id);
    $this->form = new SendMessageForm($this->message, array('send_member_id' => $send_member_id));
    if ($request->isMethod('post'))
    {
      $params = $request->getParameter('message');
      $this->form->bind($params, $request->getFiles('message'));

      if ($this->form->isValid())
      {
        $this->message = $this->form->save();
        return sfView::SUCCESS;
      }
    }
    return sfView::INPUT;    
  }
  
 /**
  * Executes editMessage action
  * 
  * @param sfRequest $request A request object
  */
  public function executeEdit($request)
  {
    $this->message = SendMessageDataPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404unless($this->message);
    $this->forward404Unless($this->isDraftOwner());
    if ($this->message->getMessageType() == MessageTypePeer::getMessageTypeIdByName('message')) {
      $send_list = $this->message->getSendList();
      $this->forward404Unless($send_list);
      sfConfig::set('sf_navi_type', 'friend');
      sfConfig::set('sf_navi_id', $send_list[0]->getMember()->getId());
      $this->form = new SendMessageForm($this->message, array('send_member_id' =>$send_list[0]->getMember()->getId()));
      $this->setTemplate('sendToFriend');
      return sfView::INPUT;
    }
  }
  
 /**
  * Executes replyMessage action
  * 
  * @param sfRequest $request A request object
  */
  public function executeReply($request)
  {
    $message = SendMessageDataPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404unless($message);
    $this->message = new SendMessageData();
    $this->message->setMessageTypeId($message->getMessageTypeId());
    $this->message->setReturnMessageId($message->getId());
    if ($message->getThreadMessageId() != 0) {
      $this->message->setThreadMessageId($message->getThreadMessageId());
    } else {
      $this->message->setThreadMessageId($message->getId());
    }
    $this->form = new SendMessageForm($this->message, array('send_member_id' =>$message->getMemberId()));
    $this->setTemplate('sendToFriend');
    return sfView::INPUT;
  }

  /*
   * messageList
   * @param sfRequest $request A request object
   * @param str       $object_name 
   * @param str       $redirect delete->redirect
   */
  protected function messageList(sfRequest $request, $object_name, $redirect)
  {
    if ($this->pager->getNbResults()) {
      $delete_message = null;
      foreach ($this->pager->getResults() as $message) {
        $delete_message[] = $message->getId();
      }
      $this->form = new MessageDeleteForm(null, array('message' => $delete_message, 'object_name' => $object_name));
      if ($request->isMethod('post'))
      {
        $params = $request->getParameter('message');
        $this->form->bind($params);
        if ($this->form->isValid())
        {
          $this->message = $this->form->save();
          $this->redirect($redirect);
        }
      }
    } else {
      $this->form = null;
    }
  }
}
