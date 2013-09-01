<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-1-13
 * Time: 下午6:55
 * To change this template use File | Settings | File Templates.
 */
class ReferSubjectBehavior extends CBehavior
{
    protected function getReferSubjectInfo()
    {
        $owner = $this->getOwner();
        return array(
            'type' => $owner->subject_type,
            'id' => $owner->subject_id
        );
    }

    protected $_subject = null;

    public function getReferSubject()
    {
        $subject_info = $this->getReferSubjectInfo();
        if (!empty($this->_subject) &&
            $subject_info['id'] == $this->_subject->id &&
            $subject_info['type'] == $this->_subject->tableName()
        ) {
            return $this->_subject;
        }
        $subject = Common::getSubjectObject($subject_info['type'], $subject_info['id']);
        $this->_subject = $subject::model()->findByPk($subject->id);
        return $this->_subject;
    }
}
