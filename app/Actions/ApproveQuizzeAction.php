<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ApproveQuizzeAction extends AbstractAction
{
    public function getTitle()
    {
        if ($this->data->{'status'} == "pending") {
            return "approve";
        } else if ($this->data->{'status'} == "approve") {
            return "reject";
        } else if ($this->data->{'status'} == "reject") {
            return "approve";
        }
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return "approve-quizze/" . $this->data->{$this->data->getKeyName()};
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'quizzes';
    }

}
