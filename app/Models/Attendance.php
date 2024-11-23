<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    protected $fillable = [
        'participant_id',
        'id_scan',
        'sqan_at',
        'sqan_by',

    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, "participant_id", "id");
        // Assuming 'participant_id' in the 'attendance' table is a foreign key referencing 'id' in the 'participants' table
    }

    public function scan()
    {
        return $this->belongsTo(Scan::class, "id_scan", "id");
        // Assuming 'id_scan' in the 'attendance' table is a foreign key referencing 'id' in the'scans' table
    }
}
