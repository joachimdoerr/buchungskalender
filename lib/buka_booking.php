<?php
class buka_booking extends rex_yform_manager_dataset {

    public static function get_query () {
        $query = self::query()
        ->selectRaw("YEAR(datestart)",'startyear')
        ->selectRaw("YEAR(dateend)",'endyear')
        ;

        if ($object_id = rex_request('object_id','int')) {
            
            $related = self::find_related($object_id);
            if (count($related)) {
                $related_ids = [$object_id=>$object_id];
                foreach ($related as $o) {
                    $related_ids[$o->id] = $o->id;
                }
                $query->whereRaw('FIND_IN_SET(object_id,:ids)',['ids'=>implode(',',$related_ids)]);
            } else {
                $query->where('object_id',rex_request('object_id','int'));
            }
        }
        return $query;
    }

    public static function find_related ($object_id) {
        $object = buka_objects::get_query()->where('id',$object_id)->findOne();
        $related = $object->find_related();
        return $related;
    }


    public static function get_min_year () {
        $first = self::get_query()->orderBy('datestart')->findOne();
        return $first->startyear ?? '';
    }
    
    public static function get_max_year () {
        $first = self::get_query()->orderBy('dateend','DESC')->findOne();
        return $first->endyear ?? '';
    }


}