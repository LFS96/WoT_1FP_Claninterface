<?php
namespace App\Model\Table;

use App\Model\Entity\Meeting;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Meetings Model
 *
 * @property ClansTable&BelongsTo $Clans
 * @property MeetingparticipantsTable&HasMany $Meetingparticipants
 * @property MeetingregistrationsTable&HasMany $Meetingregistrations
 *
 * @method Meeting get($primaryKey, $options = [])
 * @method Meeting newEntity($data = null, array $options = [])
 * @method Meeting[] newEntities(array $data, array $options = [])
 * @method Meeting|false save(EntityInterface $entity, $options = [])
 * @method Meeting saveOrFail(EntityInterface $entity, $options = [])
 * @method Meeting patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Meeting[] patchEntities($entities, array $data, array $options = [])
 * @method Meeting findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MeetingsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('meetings');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Clans', [
            'foreignKey' => 'clan_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Meetingparticipants', [
            'foreignKey' => 'meeting_id',
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Meetingregistrations', [
            'foreignKey' => 'player_id',
            'cascadeCallbacks' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->date('date')
            ->requirePresence('date', 'create')
            ->notEmptyDate('date');

        $validator
            ->time('start')
            ->requirePresence('start', 'create')
            ->notEmptyTime('start');

        $validator
            ->time('end')
            ->requirePresence('end', 'create')
            ->notEmptyTime('end');

        $validator
            ->integer('cloned')
            ->notEmptyString('cloned');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
: RulesChecker
    {
        $rules->add($rules->existsIn(['clan_id'], 'Clans'));

        return $rules;
    }
}
