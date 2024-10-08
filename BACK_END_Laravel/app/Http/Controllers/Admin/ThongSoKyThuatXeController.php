<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 

use App\Models\ThongSoKyThuatXe;
use App\Traits\TrangThaiTrait;
use Illuminate\Http\Request;

class ThongSoKyThuatXeController extends Controller
{

    use TrangThaiTrait;
    /**
     * @OA\post(
     *     path="/api/thongsokythuatxe/search",
     *    tags={"thongsokythuatxe"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        $totalPage = $request->input('totalPage');

        $query = ThongSoKyThuatXe::query();
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('MaThongSo', 'like', '%' . $search . '%')
                    ->orWhere('MaModel', 'like', '%' . $search . '%');
            });
        }

        $db = $query->paginate($totalPage ?? 5);

        $kq =  ['ketqua' => $db, 'timkiem' => $query];

        return $db->total() > 0 ? $this->ok($kq) : $this->errors(null);
    }
    /**
     * @OA\Get(
     *     path="/api/thongsokythuatxe/{total}",
     *    tags={"thongsokythuatxe"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */

    public function index($total = null)
    {
        $db = ThongSoKyThuatXe::paginate($total);
        return $db ? $this->ok($db) : $this->errors(null);
    }

    /**
     * @OA\delete(
     *     path="/api/thongsokythuatxe/delete/{id}",
     *    tags={"thongsokythuatxe"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */

    public function delete($id)
    {
        $db = ThongSoKyThuatXe::where('MaThongSo', $id)->first()->delete();
        return $db ? $this->ok($db) : $this->errors(null);
    }
    /**
     * @OA\delete(
     *     path="/api/thongsokythuatxe/deletes",
     *    tags={"thongsokythuatxe"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function deletes(Request $request)
    {
        $ids = $request->input('ids');

        $db = ThongSoKyThuatXe::whereIn('MaThongSo', $ids)->delete();

        return $db ? $this->ok($db) : $this->errors(null);
    }

    /**
     * @OA\post(
     *     path="/api/thongsokythuatxe/save/{id}",
     *    tags={"thongsokythuatxe"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function save(Request $res, $id = null)
    {

        $tk = $id ? ThongSoKyThuatXe::where('MaThongSo', $id)->first() : new ThongSoKyThuatXe();
        $tk->MaModel = $res->MaModel;
        $tk->TenPhienBan = $res->TenPhienBan;
        $tk->PhienBanXe = $res->PhienBanXe;
        $tk->SoChoNgoi = $res->SoChoNgoi;
        $tk->LoaiDongCo = $res->LoaiDongCo;
        $tk->MauSac = $res->MauSac;
        $tk->CongSuat = $res->CongSuat;
        $tk->MoMenXoan = $res->MoMenXoan;
        $tk->LoaiNhienLieu = $res->LoaiNhienLieu;
        $tk->LoaiHieuDong = $res->LoaiHieuDong;
        $db = $tk->save();
        return $db ? $this->ok($db) : $this->errors(null);
    }


    /**
     * @OA\Get(
     *     path="/api/thongsokythuatxe/get/{id}",
     *    tags={"thongsokythuatxe"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function getThongSoKyThuatXe($id)
    {
        $db = ThongSoKyThuatXe::find($id);
        return $db ? $this->ok($db) : $this->errors(null);
    }
}
