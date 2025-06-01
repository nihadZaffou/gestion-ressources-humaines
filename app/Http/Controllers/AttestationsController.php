<?php

namespace App\Http\Controllers;

use App\Models\Attestationtype;
use App\Models\Attestations;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttestationsController extends Controller
{
    
    public function index()
    {
        $attestations = AttestationType::all();
        return response()->json($attestations);
    }
    public function store()
    {
        $attestationType = new AttestationType();
        $attestationType->type = request('type');
        $attestationType->description = request('description');
        $attestationType->save();

        return response()->json([
            'message' => 'Attestation type created successfully',
            'data' => $attestationType
        ], 201);
    }
    public function update($id)
    {
        $attestationType = AttestationType::findOrFail($id);
        $attestationType->type = request('type');
        $attestationType->description = request('description');
        $attestationType->save();

        return response()->json([
            'message' => 'Attestation type updated successfully',
            'data' => $attestationType
        ]);
    }
    public function destroy($id)
    {
        $attestationType = AttestationType::findOrFail($id);
        $attestationType->delete();

        return response()->json([
            'message' => 'Attestation type deleted successfully'
        ]);
    }
    public function attestationDemandes()
    {
        $attestationDemandes = Attestations::with('attestationType')->get();
        return response()->json($attestationDemandes);
    }
    public function demandeAttestation()
    {
        try {
            $validated = request()->validate([
                'type_id' => 'required|exists:attestationtypes,id',
                'date_livraison' => 'required|date',
            ]);

            $validated['employe_id'] = Auth::id();
            $validated['date_demande'] = Carbon::now();
            $validated['statut'] = 'en attente';

            Attestations::create($validated);
            return response()->json([
                'message' => 'Attestation demandée avec succès',
                'data' => $validated
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la demande d\'attestation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteDemandeAttestation($id)
    {
        try {
            $attestation = Attestations::findOrFail($id);
            $attestation->delete();
            return response()->json([
                'message' => 'Demande d\'attestation supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la demande d\'attestation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // In your AttestationsController.php
    public function getMyDemandes()
    {
        try {
            $employeId = Auth::id();
            $demandes = Attestations::where('employe_id', $employeId)
                ->with(['attestationType', 'employe']) // Eager load type and employee info
                ->orderBy('date_demande', 'desc')
                ->get();
            return response()->json($demandes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération de vos demandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllDemande()
    {
        try {
            $attestations = Attestations::with([
                'employe:id,id,nom,email',
                'attestationType:id,type'
            ])->get();
            return response()->json($attestations);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des attestations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllAttestations()
    {
        try {
            $attestations = AttestationType::all();
            return response()->json($attestations);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des attestations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateStatut($id)
    {
        try {
            $attestation = Attestations::findOrFail($id);
            $attestation->statut = request('statut');
            $attestation->save();

            return response()->json([
                'message' => 'Statut mis à jour avec succès',
                'data' => $attestation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function uploadAttestationPdf(Request $request, $id)
    {
        try {
            $attestation = Attestations::findOrFail($id);

            if ($request->hasFile('pdf')) {
                if ($attestation->pdf) {
                    Storage::disk('public')->delete($attestation->pdf);
                }

                $file = $request->file('pdf');
                $path = $file->store('pdfs', 'public'); // Your 'pdfs' folder in storage/app/public
                $attestation->pdf = $path;
                $attestation->save();
                return response()->json(['message' => 'PDF téléversé avec succès', 'data' => $attestation]);
            }
            return response()->json(['message' => 'Aucun fichier PDF fourni.'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du téléversement du PDF', 'error' => $e->getMessage()], 500);
        }
    }
    // In AttestationsController.php
        public function destroyAttestaion($id)
    {   
        try {
            $attestation = Attestations::findOrFail($id);

            if ($attestation->pdf) {
                Storage::disk('public')->delete($attestation->pdf);
            }

            $attestation->delete();

            return response()->json([
                'message' => 'Attestation supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de l\'attestation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteAttestationPdf($id)
    {
        try {
            $attestation = Attestations::findOrFail($id);

            if ($attestation->pdf) {
                Storage::disk('public')->delete($attestation->pdf); // Delete file from storage
                $attestation->pdf = null;
                $attestation->save();
                return response()->json(['message' => 'PDF supprimé avec succès', 'data' => $attestation]);
            }

            return response()->json(['message' => 'Aucun PDF à supprimer pour cette attestation.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du PDF', 'error' => $e->getMessage()], 500);
        }
    }

}
