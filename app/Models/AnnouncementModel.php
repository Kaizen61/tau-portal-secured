<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $skipValidation = true;
    protected $allowedFields = ['title', 'message', 'created_by', 'created_at', 'updated_at'];
}
