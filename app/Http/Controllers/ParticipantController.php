<?php

namespace App\Http\Controllers;

use Milon\Barcode\DNS2D;
use App\Models\Participant;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ParticipantRegistered;
use Illuminate\Support\Facades\Mail;

class ParticipantController extends Controller
{
    //menampilkan view
    public function register()
    {
        return view('participant.register-participant');
    }

    public function register_store(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:20|min:10',

        ]);
        $participant = new Participant();
        $participant->name = $request->name;
        $participant->email = $request->email;
        $participant->phone = $request->phone;

        $qr_content = "meetap-" . time();
        $participant->qr_content = $qr_content;
        $result = $participant->save();

        //make pdf
        $background = url('assets/image/background-01.jpg');

        $barqode = new DNS2D();
        $qr_code = $barqode->getBarcodePNG($qr_content, "QRCODE", 100, 100, [0, 0, 0], true);

        $pdf = Pdf::loadHTML(view("participant.registration-card-pdf", compact("background", "qr_code", "participant")));
        $pdf->setOption("is_remote_enabled", true);
        $pdf->setPaper("a5", "potrait");

        if (!is_dir(public_path("uploads/id_cards"))) {
            mkdir(public_path("uploads/id_cards"), 0777, true);
        }

        $pdf->save(public_path("uploads/id_cards/" . $qr_content . ".pdf"));

        //send mail
        // dd("test");
        Mail::to($participant->email)->send(new ParticipantRegistered($participant, null, public_path("uploads/id_cards/" . $qr_content . ".pdf")));

        return redirect("/participant/register")->with('status', 'data berhasil disimpan silahkan cek email anda');
    }
}
