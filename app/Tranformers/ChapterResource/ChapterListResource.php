<?php


namespace App\Tranformers\ChapterResource;


use App\Tranformers\ApiResource;

class ChapterListResource extends ApiResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
//        $employee_infos = collect();
//        $this->resource->each(function ($guide,$index) use (&$employee_infos) {
//            $employee_infos->push([
//                'index' => $index+1,
//                'id' => $guide->id,
//                'fullname' => $guide->fullname,
//                'ssn' => $guide->ssn,
//                'code' => $guide->code,
//                'email' => $guide->has_employee ? $guide->has_employee->email:'',
//                'telephone' => $guide->telephone,
//                'image' => $guide->image,
//                'department_id' => $guide->department_id,
//                'department' => $guide->department ? $guide->department->name : null,
//                'part_id' => $guide->part_id,
//                'part' => $guide->part ? $guide->part->name : null,
//                'team_id' => $guide->team_id,
//                'team' => $guide->team ? $guide->team->name : null,
//                'company_id' => $guide->company_id,
//                'company' => $guide->company ? $guide->company->name : null,
//
//            ]);
//        });
//
        return $this->resource->toArray();
    }
}
