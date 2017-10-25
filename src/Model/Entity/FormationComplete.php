<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FormationComplete Entity
 *
 * @property int $id
 * @property int $employee_id
 * @property int $formation_id
 * @property \Cake\I18n\FrozenDate $lastTime_completed
 * @property string $comment
 *
 * @property \App\Model\Entity\Employee $employee
 * @property \App\Model\Entity\Formation $formation
 */
class FormationComplete extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
