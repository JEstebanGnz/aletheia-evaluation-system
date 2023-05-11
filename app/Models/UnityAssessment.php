<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\UnityAssessment
 *
 * @property int $id
 * @property int $evaluated_id
 * @property int $evaluator_id
 * @property string $role
 * @property int $pending
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $evaluated
 * @property-read \App\Models\User $evaluator
 * @property-read \App\Models\FormAnswers|null $formAnswer
 * @property-read \App\Models\Unit $unity
 * @method static \Database\Factories\UnityAssessmentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment whereEvaluatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment whereEvaluatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment wherePending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnityAssessment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Unit $unit
 */
class UnityAssessment extends Model
{
    use HasFactory;

    protected $guarded = [];


    public static function assignRolesToTeacher($beingAssignedUserId, $assignedToUserId, $role): void{

        //Si el usuario que desea asignarle es el mismo, pues lo saca de una vez
        if($beingAssignedUserId == $assignedToUserId){
            throw new \RuntimeException('El docente no puede ser su propio par/jefe');
        }

        /*Si ya hay un par o jefe asignado, se encarga de que no se pueda colocar a esa misma persona como jefe o par*/
        $record = DB::table('unity_assessments')->where('evaluated_id', $beingAssignedUserId)
            ->where('evaluator_id', $assignedToUserId)->exists();

        if($record){

            throw new \RuntimeException('Ese docente ya es par/jefe del respectivo docente');
        }

        if($role == "jefe"){

            $roleId = Role::getRoleIdByName('jefe de profesor');;

        }

        else{

            $roleId = Role::getRoleIdByName($role);
        }

        if(!$record){

                $teacherWithRole = DB::table('role_user')->where('user_id', $assignedToUserId)->where('role_id', $roleId)->first();

                if(!$teacherWithRole){

                    DB::table('role_user')->updateOrInsert(['user_id'=> $assignedToUserId, 'role_id' => $roleId]);

                }

                UnityAssessment::updateOrCreate(
                    ['evaluated_id' => $beingAssignedUserId,
                        'role' => $role],
                    [ 'evaluator_id' => $assignedToUserId,
                        'pending' => true]);


        }

    }


    public static function removeAssignment($beingAssignedUserId, $assignedToUserId, $role): void{

        $record = DB::table('unity_assessments')->where('evaluated_id', $beingAssignedUserId)
            ->where('evaluator_id', $assignedToUserId)->where('role', $role)->first();


        //Aqui simplemente buscamos el registro asociado a esa asignación y lo borramos
        if($record){

            DB::table('unity_assessments')->where('evaluated_id', $beingAssignedUserId)
                ->where('evaluator_id', $assignedToUserId)->where('role', $role)->delete();

        }


        //Aqui verificamos si el usuario al que fue asignado, tiene otras asignaciones adicionales...

        $user = DB::table('unity_assessments')->where('evaluator_id', $assignedToUserId)
            ->where('role', $role)->get();


        if($role == "jefe"){

            $role= "jefe de profesor";

        }

        $roleId = Role::getRoleIdByName($role);

    //Si se llega a dar que esa asignación que borramos era la última para el correspondiente rol, entonces procedemos a
        //borrarle ese rol en la tabla role_user
        if($user->count() == 0){

            DB::table('role_user')->where('user_id', $assignedToUserId)
                ->where('role_id', $roleId)->delete();

        }


    }



    public static function getAllAssignments(){

           return self::get();
    }


    public static function getUnitAssignments($unitTeachersId){

        $finalTeachers = DB::table('unity_assessments')
            ->whereIn('unity_assessments.evaluated_id', $unitTeachersId)
            ->join('users','users.id','unity_assessments.evaluator_id')->get();

        return $finalTeachers;

    }


    public function evaluated(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'evaluated_id');
    }
    public function evaluator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'evaluator_id');
    }
    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
    public function formAnswer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FormAnswers::class);
    }
}
