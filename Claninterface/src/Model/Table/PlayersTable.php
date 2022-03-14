<?php
namespace App\Model\Table;

use App\Model\Entity\Player;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use PhpParser\Node\Stmt\HaltCompiler;

/**
 * Players Model
 *
 * @property ClansTable&BelongsTo $Clans
 * @property RanksTable&BelongsTo $Ranks
 * @property InactivesTable&HasMany $Inactives
 * @property StatisticsTable&HasMany $Statistics
 * @property TeamspeaksTable&HasMany $Teamspeaks
 * @property TokensTable&HasMany $Tokens
 * @property UsersTable&HasMany $Users
 * @property MeetingparticipantsTable&HasMany $Meetingparticipants
 * @property MeetingregistrationsTable&HasMany $Meetingregistrations
 *
 * @method Player get($primaryKey, $options = [])
 * @method Player newEntity($data = null, array $options = [])
 * @method Player[] newEntities(array $data, array $options = [])
 * @method Player|false save(EntityInterface $entity, $options = [])
 * @method Player saveOrFail(EntityInterface $entity, $options = [])
 * @method Player patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Player[] patchEntities($entities, array $data, array $options = [])
 * @method Player findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin TimestampBehavior
 */
class PlayersTable extends Table
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

        $this->setTable('players');
        $this->setDisplayField('nick');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Clans', [
            'foreignKey' => 'clan_id',
        ]);
        $this->belongsTo('Ranks', [
            'foreignKey' => 'rank_id',
        ]);
        $this->hasMany('Inactives', [
            'foreignKey' => 'player_id',
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Statistics', [
            'cascadeCallbacks' => true,
            'foreignKey' => 'player_id',
        ]);
        $this->hasMany('Teamspeaks', [
            'foreignKey' => 'player_id',
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Tokens', [
            'foreignKey' => 'player_id',
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'player_id',

        ]);
        $this->hasMany('Meetingparticipants', [
            'foreignKey' => 'player_id',
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
     * @param Validator $validator Validator instance.
     * @return Validator the Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('nick')
            ->maxLength('nick', 255)
            ->requirePresence('nick', 'create')
            ->notEmptyString('nick');

        $validator
            ->dateTime('joined')
            ->requirePresence('joined', 'create')
            ->notEmptyDateTime('joined');

        $validator
            ->dateTime('lastBattle')
            ->requirePresence('lastBattle', 'create')
            ->notEmptyDateTime('lastBattle');

        $validator
            ->integer('battle')
            ->notEmptyString('battle');

        $validator->decimal("wn8",2);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules)
: RulesChecker
    {
        $rules->add($rules->existsIn(['clan_id'], 'Clans'));
        $rules->add($rules->existsIn(['rank_id'], 'Ranks'));

        return $rules;
    }
}
