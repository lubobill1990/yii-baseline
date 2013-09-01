<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property string $id
 * @property string $subject_type
 * @property integer $subject_id
 * @property string $refer_comment_id
 * @property string $user_id
 * @property string $node_level
 * @property integer $sub_comment_count
 * @property string $refer_comment_content
 * @property string $content
 * @property string $create_time
 * @property string $is_deleted
 * @property string $delete_time
 * @property string $who_delete
 * @property string $digg_count
 * @property string $bury_count
 *
 * The followings are the available model relations:
 * @property Comment $referComment
 * @property Comment[] $comments
 */
class Comment extends CActiveRecord
{
    public $child_more_comments_count = null;
    public $child_comments = array();
    public $parent_comments = array();
    public $parent_more_comments_count = null;

    protected $_atted_user_ids;

    public function behaviors()
    {
        return array(
            'ReferSubjectBehavior' => array(
                'class' => 'application.modules.feedback.behaviors.ReferSubjectBehavior',
            ),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Comment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'feedback_comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('subject_id, user_id, node_level, content', 'required'),
            array('subject_id, sub_comment_count', 'numerical', 'integerOnly' => true),
            array('subject_type', 'length', 'max' => 9),
            array('refer_comment_id, user_id, digg_count, bury_count', 'length', 'max' => 11),
            array('node_level', 'length', 'max' => 10),
            array('is_deleted', 'length', 'max' => 3),
            array('who_delete', 'length', 'max' => 20),
            array('refer_comment_content, create_time, delete_time', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, subject_type, subject_id, refer_comment_id, user_id, node_level, sub_comment_count, refer_comment_content, content, create_time, is_deleted, delete_time, who_delete, digg_count, bury_count', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'referComment' => array(self::BELONGS_TO, 'Comment', 'refer_comment_id'),
            'comments' => array(self::HAS_MANY, 'Comment', 'refer_comment_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'subject_type' => 'Subject Type',
            'subject_id' => 'Subject',
            'refer_comment_id' => 'Refer Comment',
            'user_id' => 'User',
            'node_level' => 'Node Level',
            'sub_comment_count' => 'Sub Comment Count',
            'refer_comment_content' => 'Refer Comment Content',
            'content' => 'Content',
            'create_time' => 'Create Time',
            'is_deleted' => 'Is Deleted',
            'delete_time' => 'Delete Time',
            'who_delete' => 'Who Delete',
            'digg_count' => 'Digg Count',
            'bury_count' => 'Bury Count',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('subject_type', $this->subject_type, true);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('refer_comment_id', $this->refer_comment_id, true);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('node_level', $this->node_level, true);
        $criteria->compare('sub_comment_count', $this->sub_comment_count);
        $criteria->compare('refer_comment_content', $this->refer_comment_content, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('is_deleted', $this->is_deleted, true);
        $criteria->compare('delete_time', $this->delete_time, true);
        $criteria->compare('who_delete', $this->who_delete, true);
        $criteria->compare('digg_count', $this->digg_count, true);
        $criteria->compare('bury_count', $this->bury_count, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }



    private function buildComment(&$comment, $depth, $width)
    {
        if ($depth == 0) {
            return;
        }
        $criteria = new CDbCriteria();
        $criteria->limit = $width;
        $criteria->compare('refer_comment_id', $comment->id);
        $comments = Comment::model()->findAll($criteria);
        if (count($comments) == 0) {
            return;
        }

        if (count($comments) < $width) {
            $comment->child_more_comments_count = 0;
        } else {
            $total = Comment::model()->countByAttributes(array('refer_comment_id' => $comment->id));
            $comment->child_more_comments_count = $total - count($comments);
        }

        foreach ($comments as &$cmt) {
            $comment->child_comments[] = $cmt;
            $this->buildComment($comment->child_comments[count($comment->child_comments) - 1], $depth - 1, $width);
        }
    }

    public function topDown($depth, $width)
    {
        $ret_val = $this;
        $ret_val->comments = array();
        $this->buildComment($ret_val, $depth, $width);
        return $ret_val;
    }

    public function bottomUp($depth)
    {
        $total = 0;
        $ret_val = $this;
        $comment = $ret_val;
        //TODO 这个while循环是低效率的，不需要循环那么多次
        while ($comment->referComment) {
            $comment = $comment->referComment;

            if ($depth > $total) {
                $ret_val->parent_comments[] = $comment;
            }
            ++$total;
        }

        $ret_val->parent_more_comments_count = $total > $depth ? $total - $depth : 0;
        $ret_val->parent_comments = array_reverse($ret_val->parent_comments);
        return $ret_val;
    }

    public function context($count, $order = "asc")
    {
        $cmt = $this->attributes;
        $cmt['comments'] = array();

        $c = new CDbCriteria();
        $c->order = "create_time ${order}";
        if (is_null($this->refer_comment_id)) {
            $c->condition = "subject_id = '" . $this->subject_id . "' AND subject_type= '" . $this->subject_type . "' AND refer_comment_id is NULL ";
        } else {
            $c->compare('refer_comment_id', $this->refer_comment_id);
        }

        $comments = Comment::model()->findAll($c);
        $total = count($comments);

        $i = 0;
        for (; $i < $total; ++$i) {
            if ($comments[$i]->id == $this->id) {
                break;
            }
        }
        for ($j = $i + 1; $j < $total && $j - $i <= $count; ++$j) {
            $cmt['comments'][] = $comments[$j]->attributes;
        }
        $cmt['more_comments_count'] = $total > $count ? $total - $count - 1 : 0;
        return $cmt;
    }

    public function subject()
    {
        if ($this->subject_type === 'anthology') {
            echo CJSON::encode(Anthology::model()->findByPk($this->subject_id));
        } else {
            echo CJSON::encode(Article::model()->findByPk($this->subject_id));
        }
    }

    public function getUrl()
    {
        return "/comment/{$this->id}";
    }

    public static function getLatest($subject_type, $subject_id, $start = 0, $limit = 20)
    {
        return self::model()->findAllByAttributes(
            array('subject_type' => $subject_type,
                'subject_id' => $subject_id,),
            array('offset' => $start, 'limit' => $limit)
        );
    }

    public function beforeValidate()
    {
        if (empty($this->refer_comment_id) ||
            empty($this->referComment) ||
            $this->referComment->subject_type != $this->subject_type ||
            $this->referComment->subject_id != $this->subject_id
        ) {
            $this->refer_comment_id = null;
            $this->node_level = 1;
        } else {
            $this->node_level = $this->referComment->node_level + 1;
            $this->refer_comment_content = $this->referComment->content;
        }
        if (parent::beforeValidate()) {
            return true;
        }
        return false;
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            //为@的用户加上链接
            $this->_atted_user_ids = Common::getAttedUserIdsFromString($this->content, $this->user_id);
            $this->content = Common::addLinkToAttedUsersInString($this->content, $this->_atted_user_ids);
            return true;
        }
        return false;
    }

    public function afterSave()
    {
        parent::afterSave();
        if($this->isNewRecord){
            $this->refresh();
        }
        try {
            $subject = Common::getSubjectObject($this->subject_type, $this->subject_id);
        } catch (Exception $e) {
            return;
        }
        $subject->incrCommentCount();

        //如果有重复，则只发上一层优先级的提醒
        $already_remind_user_id_array = array();
        //如果是对上一层的评论进行评论，则向上一层评论的主人发送提醒
        if (!empty($this->refer_comment_id) &&
            !empty($this->referComment) &&
            !array_key_exists($this->referComment->user_id, $already_remind_user_id_array)
        ) {
            Remind::sendRemind(
                $this->user,
                $this->referComment->user_id,
                'comment', $this->id, 'comment', 'comment', $this->referComment->id
            );
            $already_remind_user_id_array[$this->referComment->user_id] = true;
        }
        //如果是第一层的评论，则向评论所在的subject的主人发送提醒
        if ($this->node_level == 1) {
            $refer_subject = $this->getReferSubject();
            if (isset($refer_subject->user_id) &&
                !array_key_exists($refer_subject->user_id, $already_remind_user_id_array)
            ) {
                Remind::sendRemind(
                    $this->user,
                    $refer_subject->user_id,
                    'comment', $this->id, 'comment', $refer_subject->tableName(), $refer_subject->id
                );
                $already_remind_user_id_array[$refer_subject->user_id] = true;
            }
        }
        //如果内容中有@到某些人，则向被at的人提醒
        if (!empty($this->_atted_user_ids)) {
            $refer_subject = $this->getReferSubject();
            foreach ($this->_atted_user_ids as $user_id) {
                if (!array_key_exists($user_id, $already_remind_user_id_array)) {
                    Remind::sendRemind(
                        $this->user,
                        $user_id,
                        'at',
                        $this->id, 'comment',
                        $refer_subject->tableName(),
                        $refer_subject->id
                    );
                }
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $subject = Common::getSubjectObject($this->subject_type, $this->subject_id);
        $subject->decrCommentCount();
        //TODO 删掉已经发送的remind
    }

    public function getShortReferCommentContent()
    {
        return mb_substr($this->refer_comment_content, 0, Yii::app()->params['feedback']['short_comment_content_length']);
    }

    private $_isShortReferCommentCut = null;

    public function isShortReferCommentCut()
    {
        return $this->_isShortReferCommentCut === null ? $this->_isShortReferCommentCut = mb_strlen($this->refer_comment_content) > Yii::app()->params['feedback']['short_comment_content_length'] : $this->_isShortReferCommentCut;
    }

    public static function countOfSubject($subject_type,$subject_id){
        try {
            $comment_count = Common::getSubjectObject($subject_type, $subject_id)->getCommentCount();
        } catch (Exception $e) {
            $comment_count = Comment::model()->count("subject_id=:subject_id AND subject_type=:subject_type", array('subject_type' => $subject_type, 'subject_id' => $subject_id));
        }
        return $comment_count;
    }
}