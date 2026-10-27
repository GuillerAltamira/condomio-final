<?php

namespace App\Http\Controllers;

use App\Models\AreaComun;
use Illuminate\Http\Request;

class AreaComunController extends Controller
{
    public function index()
    {
        $areas = AreaComun::withCount(['reservas', 'reservasConfirmadas'])
            ->orderBy('nombre')
            ->get();

        return view('areas-comunes.index', compact('areas'));
    }

    public function create()
    {
        return view('areas-comunes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:500'],
            'costo_reserva' => ['required', 'numeric', 'min:0'],
            'normas' => ['nullable', 'string'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,JPG,JPEG,PNG', 'max:5120'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data = [
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'capacidad' => $validated['capacidad'],
            'costo_reserva' => $validated['costo_reserva'],
            'normas' => $validated['normas'] ?? null,
            'activo' => $request->boolean('activo', true),
        ];

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ruta física exacta apuntando a la carpeta pública visible para la web
            $destinationPath = base_path('public/storage/areas_comunes');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            
            // Guardamos la ruta limpia que espera el modelo
            $data['imagen'] = 'areas_comunes/' . $filename;
        }

        $area = AreaComun::create($data);

        return redirect()->route('areas-comunes.index')
            ->with('success', "Área común '{$area->nombre}' creada exitosamente.");
    }

    public function edit(AreaComun $areaComun)
    {
        return view('areas-comunes.edit', compact('areaComun'));
    }

    public function update(Request $request, AreaComun $areaComun)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:500'],
            'costo_reserva' => ['required', 'numeric', 'min:0'],
            'normas' => ['nullable', 'string'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,JPG,JPEG,PNG', 'max:5120'],
            'activo' => ['nullable', 'boolean'],
        ]);

        $data = [
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'capacidad' => $validated['capacidad'],
            'costo_reserva' => $validated['costo_reserva'],
            'normas' => $validated['normas'] ?? null,
            'activo' => $request->boolean('activo', true),
        ];

        if ($request->hasFile('imagen')) {
            // Borrar la foto anterior si existe físicamente en la carpeta pública
            if ($areaComun->imagen) {
                // Limpiamos prefijos por seguridad al buscar el archivo viejo
                $cleanOldPath = str_replace(['public/', 'storage/'], '', $audioPath ?? $areaComun->imagen);
                $oldPath = base_path('public/storage/' . $cleanOldPath);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('imagen');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = base_path('public/storage/areas_comunes');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['imagen'] = 'areas_comunes/' . $filename;
        }

        $areaComun->update($data);

        return redirect()->route('areas-comunes.index')
            ->with('success', "Área común '{$areaComun->nombre}' actualizada correctamente.");
    }

    public function destroy(AreaComun $areaComun)
    {
        if ($areaComun->imagen) {
            $cleanOldPath = str_replace(['public/', 'storage/'], '', $areaComun->imagen);
            $imagePath = base_path('public/storage/' . $cleanOldPath);
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        $areaComun->delete();

        return redirect()->route('areas-comunes.index')
            ->with('success', 'Área común eliminada.');
    }
}