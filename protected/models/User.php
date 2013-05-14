<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $has_been_activated
 * @property string $blocked
 *
 * The followings are the available model relations:
 * @property UserOperationKey $operationKeys
 */
class User extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
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
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email', 'length', 'max' => 255),
            array('password, salt,username', 'length', 'max' => 40),
            array('has_been_activated, blocked', 'length', 'max' => 3),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, email,username, password, salt, has_been_activated, blocked', 'safe', 'on' => 'search'),
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
            'userActivateCode' => array(self::HAS_ONE, 'UserActivateCode', 'user_id'),
            'changePasswordKey' => array(self::HAS_ONE, 'UserChangePasswordKey', 'user_id'),
            'operationKeys' => array(self::HAS_MANY, 'UserOperationKey', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'email' => 'Email',
            'username' => 'User Name',
            'password' => 'Password',
            'salt' => 'Salt',
            'has_been_activated' => 'Has Been Activated',
            'blocked' => 'is blocked',
        );
    }

    public function authorizePassword($raw_password)
    {
        return trim($this->password) == trim(md5(md5($raw_password) . $this->salt));
    }

    public function changePassword($password)
    {
        $password = trim($password);
        if (!empty($password)) {
            $salt = Common::generateRandomString(40);
            $password = md5(md5($password) . $salt);
        }
        $this->salt = $salt;
        $this->password = $password;
    }

    public function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->salt = Common::generateRandomString(40);
            $this->password = trim(md5(md5($this->password) . $this->salt));
        }
        return true;
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
        $criteria->compare('email', $this->email, true);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('salt', $this->salt, true);
        $criteria->compare('has_been_activated', $this->has_been_activated, true);
        $criteria->compare('blocked', $this->blocked, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function generateOperationKey($operation)
    {
        $key = Common::generateRandomString(60);
        $command = Yii::app()->db->createCommand("REPLACE INTO " . UserOperationKey::model()->tableName() . "(user_id,operation,`key`) values(:user_id ,:operation,:key)");
        $command->execute(array('user_id' => $this->id, 'operation' => $operation, 'key' => $key));
        return $key;
    }

    public function getOperationKey($operation)
    {
        return UserOperationKey::model()->findByAttributes(array('operation' => $operation, 'user_id' => $this->id));
    }
}