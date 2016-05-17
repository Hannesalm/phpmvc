<?php

namespace Anax\Events;

/**
 * Model for Events.
 *
 */
class Event extends \Anax\Users\CDatabaseModel {

    public function getSource()
    {
        return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
    }

    public function getProperties()
    {
        $properties = get_object_vars($this);
        unset($properties['di']);
        unset($properties['db']);

        return $properties;
    }

    public function setProperties($properties)
    {
        // Update object with incoming values, if any
        if (!empty($properties)) {
            foreach ($properties as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function findAllOfMonth($month, $year)
    {
        $start_date = "showdate >= '".$year ."-".$month ."-01'";
        $end_date = "showdate <= '".$year ."-".$month ."-31'";

        $this->db->select()
            ->from($this->getSource())
            ->where($start_date)
            ->andWhere($end_date)
            ->execute();

        return $this->db->fetchAll($this);
    }

    public function findEventsOfDay($date){

        $currentDate = "'". $date ."'" ;
        $this->db->select()
            ->from($this->getSource())
            ->where('showdate = '.$currentDate)
            ->execute();

        return $this->db->fetchAll($this);
    }

    /**
     *
     * @return array with eventcount per day.
     */
    public function getEventCountPerDayOfMonth($month,$year = '2016'){
        $eventsPerDayForMonth = [];

        for($i = 1;$i < 32;$i++){
            $day = sprintf('%02s', $i);
            $date = "'".$year ."-".$month ."-".$day."'";
            $count = $this->getEventCount($date);
            $eventsPerDayForMonth[$i] = $count;
        }

        return $eventsPerDayForMonth;
    }
    public function getEventCount($date){
        $this->db->select()
            ->from($this->getSource())
            ->where('showdate = '.$date)
            ->execute();
        $count = $this->db->fetchAll($this);

        return $result = count($count);

    }

    public function save($values = [])
    {
        $this->setProperties($values);
        $values = $this->getProperties();

        if (isset($values['id'])) {
            return $this->update($values);
        } else {
            return $this->create($values);
        }
    }

    public function create($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);

        $this->db->insert(
            $this->getSource(),
            $keys
        );

        $res = $this->db->execute($values);

        $this->id = $this->db->lastInsertId();

        return $res;
    }

    public function update($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);

        unset($keys['id']);
        $values[] = $this->id;


        $this->db->update(
            $this->getSource(),
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function find($id)
    {
        $this->db->select()
            ->from($this->getSource())
            ->where("id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchInto($this);
    }

    public function delete($id)
    {

        $this->db->delete(
            $this->getSource(),
            'id = ?'
        );

        return $this->db->execute([$id]);
    }


}