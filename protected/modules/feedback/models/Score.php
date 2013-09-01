<?php

/**
 * This is the model class for table "score".
 *
 * The followings are the available columns in table 'score':
 * @property string $id
 * @property string $subject_type
 * @property string $subject_id
 * @property double $score
 * @property double $total_score
 * @property integer $star_user_count
 *
 * The followings are the available model relations:
 * @property ScoreItem[] $scoreItems
 */
class Score extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Score the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'score';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('subject_id', 'required'),
			array('star_user_count', 'numerical', 'integerOnly'=>true),
			array('score, total_score', 'numerical'),
			array('subject_type', 'length', 'max'=>9),
			array('subject_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, subject_type, subject_id, score, total_score, star_user_count', 'safe', 'on'=>'search'),
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
			'scoreItems' => array(self::HAS_MANY, 'ScoreItem', 'score_id'),
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
			'score' => 'Score',
			'total_score' => 'Total Score',
			'star_user_count' => 'Star User Count',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('subject_type',$this->subject_type,true);
		$criteria->compare('subject_id',$this->subject_id,true);
		$criteria->compare('score',$this->score);
		$criteria->compare('total_score',$this->total_score);
		$criteria->compare('star_user_count',$this->star_user_count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}