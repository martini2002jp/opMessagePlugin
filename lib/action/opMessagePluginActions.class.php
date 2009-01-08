<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * base actions class for the opMessagePlugin.
 *
 * @package    OpenPNE
 * @subpackage message
 * @author     Maki TAKAHASHI <maki@jobweb.co.jp>
 */
class opMessagePluginActions extends sfActions
{
  protected function isDraftOwner()
  {
    if ($this->message->getMemberId() !== $this->getUser()->getMemberId()) {
      return false;
    }
    if ($this->message->getIsSend() === 1) {
      return false;
    }
    return true;
  }
}
