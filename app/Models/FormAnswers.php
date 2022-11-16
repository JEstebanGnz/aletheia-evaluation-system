<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\FormAnswers
 *
 * @property int $id
 * @property int $user_id
 * @property int $form_questions_id
 * @property mixed $questions
 * @property mixed $answers
 * @property string $submitted_at
 * @property int $group_user_id
 * @property int|null $unity_assessment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Form|null $form
 * @property-read \App\Models\UnityAssessment|null $unityAssessment
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\FormAnswersFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereFormQuestionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereGroupUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereUnityAssessmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormAnswers whereUserId($value)
 * @mixin \Eloquent
 */
class FormAnswers extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createStudentFormFromRequest(Request $request, Form $form): void
    {
        $competencesAverage = self::getCompetencesAverage($request->input('answers'));
        self::create([
            'user_id' => auth()->user()->id,
            'form_id' => $form->id,
            'answers' => json_encode($request->input('answers')),
            'submitted_at' => Carbon::now()->toDateTimeString(),
            'group_id' => $request->input('groupId'),
            'teacher_id' => $request->input('teacherId'),
        ]);

        self::updateResponseStatusToAnswered($request->input('groupId'), $request->input('teacherId'));
    }

    public static function getCompetencesAverage($answers)
    {
        $answersAsObject = json_decode($answers, false);
        $competences = self::getCompetencesFromFormAnswer($answersAsObject);
        $averages = self::getAveragesFromCompetences($competences);
        dd($averages);
    }

    private static function getAveragesFromCompetences($competences)
    {
        $averages = [];
        foreach ($competences as $competence => $attributes) {
            $averages[$competence] = $attributes['accumulatedValue'] / $attributes['totalAnswers'];
        }
        return $averages;
    }

    private static function getCompetencesFromFormAnswer($formAnswers): array
    {
        $competences = [];
        foreach ($formAnswers as $answer) {
            if (isset($competences[$answer->competence]) === true) {
                $competences[$answer->competence]['totalAnswers']++;
            } else {
                $competences[$answer->competence]['totalAnswers'] = 1;
            }

            // the competence already exist at this point
            if (isset($competences[$answer->competence]['accumulatedValue']) === true) {
                $competences[$answer->competence]['accumulatedValue'] += (double)$answer->answer;
            } else {
                $competences[$answer->competence]['accumulatedValue'] = (double)$answer->answer;
            }
        }
        return $competences;
    }

    public static function updateResponseStatusToAnswered($groupId, $userId): void
    {
        DB::table('group_user')
            ->where('group_id', '=', $groupId)
            ->where('user_id', '=', $userId)
            ->update(
                [
                    'has_answer' => 1
                ]
            );

    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function groupUser()
    {
        DB::table('group_user')->select('*')->join('form_answers', 'form_answers.group_user_id', '=', 'group_user.id')
            ->where('form_answers.group_user_id', '=', $this->group_user_id)->get();
    }

    public function unityAssessment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UnityAssessment::class);
    }

    public function form(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Form  ::class);
    }
}
