<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

class ListingController
{
    protected Database $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')
            ->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create()
    {
        loadView('listings/create');
    }

    /**
     * Show a single listing
     * @param array $params
     * @return void
     */
    public function show($params)
    {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in Database
     * @return void
     */
    public function store()
    {
        $allowedFields = [
            'title', 'description', 'salary', 'tags',
            'company', 'address', 'city', 'state',
            'phone', 'email', 'requirements', 'benefits'
        ];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));
        $newListingData['user_id'] = Session::get('user')['id'];
        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            // Submit data
            $fields = [];
            foreach($newListingData as $field => $value) {
                $fields[] = $field;
            }
            $fields = implode(', ', $fields);
            $values = [];
            foreach ($newListingData as $field => $value) {
                // Convert empty strings to null
                if ($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }
            $values = implode(', ', $values);
            $query = "INSERT INTO listings({$fields}) VALUES({$values})";
            $this->db->query($query, $newListingData);

            Session::setFlashMessage('success_message', 'Listing created successfully');

            redirect('/listings');
        }
    }

    /**
     * Delete a listing
     * @param array $params
     * @return void
     */
    public function destroy($params)
    {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            exit();
        }

        // Authorization
        if (!Authorization::isOwner($listing['user_id'])) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete the listing');
            return redirect('/listings/' . $listing['id']);
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', [
            'id' => $id
        ]);

        // Set flash message
        Session::setFlashMessage('success_message', 'Listing deleted successfully');

        redirect('/listings');
    }

    /**
     * SHow the listing edit form
     * @param $params
     * @return void
     */
    public function edit($params)
    {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * @param $params
     * @return void
     */
    public function update($params)
    {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        $allowedFields = [
            'title', 'description', 'salary', 'tags',
            'company', 'address', 'city', 'state',
            'phone', 'email', 'requirements', 'benefits'
        ];

        $updatedValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updatedValues['user_id'] = 1;

        $updatedValues = array_map('sanitize', $updatedValues);
        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];
        $errors = [];
        foreach ($requiredFields as $field) {
            if (empty($updatedValues[$field]) || !Validation::string($updatedValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit();
        } else {
            $updateFields = [];
            foreach (array_keys($updatedValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            }

            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listings SET {$updateFields} WHERE id = :id";
            $updatedValues['id'] = $id;
            $this->db->query($updateQuery, $updatedValues);

            Session::setFlashMessage('success_message', 'Listing Updated');
            redirect("/listings/{$id}");
        }
    }
}