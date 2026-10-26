<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\AnnouncementModel;

class AnnouncementController extends BaseController
{
    public function index()
    {
        return $this->render();
    }

    public function edit(int $id)
    {
        $announcement = (new AnnouncementModel())->find($id);
        if (!$announcement) {
            session()->setFlashdata('error', 'Announcement not found.');
            return redirect()->to('admin/announcements');
        }

        return $this->render($announcement);
    }

    public function create()
    {
        if (!$this->validate([
            'title' => 'required|min_length[2]|max_length[150]',
            'message' => 'required|min_length[2]|max_length[10000]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('admin/announcements');
        }

        $title = trim((string) $this->request->getPost('title'));
        $message = trim((string) $this->request->getPost('message'));
        if (mb_strlen($title) < 2 || mb_strlen($message) < 2) {
            session()->setFlashdata('error', 'Title and message must each contain at least 2 non-space characters.');
            return redirect()->to('admin/announcements');
        }

        $announcements = new AnnouncementModel();
        $id = $announcements->insert([
            'title' => $title,
            'message' => $message,
            'created_by' => session()->get('username'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
        ]);
        if (!$id) {
            session()->setFlashdata('error', 'Could not publish the announcement. Please try again.');
            return redirect()->to('admin/announcements');
        }

        $this->logAction('ANNOUNCEMENT_CREATED', "Created announcement #$id: $title");
        session()->setFlashdata('saved', 'Announcement published.');
        return redirect()->to('admin/announcements');
    }

    public function update(int $id)
    {
        $announcements = new AnnouncementModel();
        if (!$announcements->find($id)) {
            session()->setFlashdata('error', 'Announcement not found.');
            return redirect()->to('admin/announcements');
        }

        if (!$this->validate([
            'title' => 'required|min_length[2]|max_length[150]',
            'message' => 'required|min_length[2]|max_length[10000]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to("admin/announcements/$id/edit");
        }

        $title = trim((string) $this->request->getPost('title'));
        $message = trim((string) $this->request->getPost('message'));
        if (mb_strlen($title) < 2 || mb_strlen($message) < 2) {
            session()->setFlashdata('error', 'Title and message must each contain at least 2 non-space characters.');
            return redirect()->to("admin/announcements/$id/edit");
        }
        if (!$announcements->update($id, [
            'title' => $title,
            'message' => $message,
            'updated_at' => date('Y-m-d H:i:s'),
        ])) {
            session()->setFlashdata('error', 'Could not update the announcement. Please try again.');
            return redirect()->to("admin/announcements/$id/edit");
        }

        $this->logAction('ANNOUNCEMENT_UPDATED', "Updated announcement #$id: $title");
        session()->setFlashdata('saved', 'Announcement updated.');
        return redirect()->to('admin/announcements');
    }

    public function delete(int $id)
    {
        $announcements = new AnnouncementModel();
        $announcement = $announcements->find($id);
        if (!$announcement) {
            session()->setFlashdata('error', 'Announcement not found.');
            return redirect()->to('admin/announcements');
        }
        if (!$announcements->delete($id)) {
            session()->setFlashdata('error', 'Could not delete the announcement. Please try again.');
            return redirect()->to('admin/announcements');
        }

        $this->logAction('ANNOUNCEMENT_DELETED', "Deleted announcement #$id: {$announcement['title']}");
        session()->setFlashdata('saved', 'Announcement deleted.');
        return redirect()->to('admin/announcements');
    }

    private function render(?array $announcement = null)
    {
        $q = mb_substr(trim((string) $this->request->getGet('q')), 0, 100);
        $query = (new AnnouncementModel())->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');
        if ($q !== '') {
            $query->groupStart()
                ->like('title', $q)
                ->orLike('message', $q)
                ->orLike('created_by', $q)
                ->groupEnd();
        }

        return view('admin/announcements', [
            'pageTitle' => 'Announcements - TAU Student Portal',
            'activePage' => 'announcements',
            'eyebrow' => 'Administration',
            'pageHeading' => 'School Announcements',
            'announcements' => $query->findAll(),
            'q' => $q,
            'announcement' => $announcement,
            'saved' => session()->getFlashdata('saved'),
            'error' => session()->getFlashdata('error'),
        ]);
    }

    private function logAction(string $action, string $details): void
    {
        (new ActivityLogger())->log(
            session()->get('user_id'),
            session()->get('username'),
            $action,
            $details,
            'INFO'
        );
    }
}
