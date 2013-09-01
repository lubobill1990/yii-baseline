<?php

/**
 * This is the model class for table "like".
 * 要使用Like的subject，一定有个id为名称的主键
 *
 * The followings are the available columns in table 'like':
 * @property string $subject_type
 * @property integer $subject_id
 * @property integer $user_id
 */
class Like extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Like the static model class
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
        return 'feedback_like';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('subject_id, user_id', 'numerical', 'integerOnly' => true),
            array('subject_type', 'length', 'max' => 9),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('subject_type, subject_id', 'safe', 'on' => 'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'subject_type' => 'Subject Type',
            'subject_id' => 'Subject',
            'user_id' => 'User',
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

        $criteria->compare('subject_type', $this->subject_type);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('user_id', $this->user_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * TODO 保存前保证subject_type的合法性
     * @return bool|void
     */
    public function beforeSave()
    {
        parent::beforeSave();
        return true;
    }

    /**
     * 如果是新增加的，则将该用户的likeCount和subject对应的count都加1
     */
    public function afterSave()
    {
        parent::afterSave();
        if ($this->isNewRecord) {
            $subjectClass = ucfirst($this->subject_type);
            $subject = new $subjectClass;
            $subject->id = $this->subject_id;
            $this->_subject_like_count = $subject->incrLikeCount();
            $user = new User();
            $user->id = $this->user_id;
            $user->incrLikeCount();
        }
    }

    /**
     * 将用户的likeCount 和subject对应的count对减一
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $subjectClass = ucfirst($this->subject_type);
        $subject = new $subjectClass;
        $subject->id = $this->subject_id;
        $this->_subject_like_count = $subject->decrLikeCount();
        $user = new User();
        $user->id = $this->user_id;
        $user->decrLikeCount();
    }

    private $_subject_like_count = null;

    public function getSubjectLikeCount()
    {
        if ($this->_subject_like_count == null) {
            $subjectClass = ucfirst($this->subject_type);
            $subject = new $subjectClass;
            $subject->id = $this->subject_id;
            $this->_subject_like_count = $subject->getLikeCount();
        }
        if (empty($this->_subject_like_count)) {
            return 0;
        }
        return $this->_subject_like_count;
    }
}