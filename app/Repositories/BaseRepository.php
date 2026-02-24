<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BaseQuery\BaseQueryTrait;

/**
 * BaseRepository
 *
 * This is the centralized “parent” repository where shared repository behavior
 * is defined. All child repositories (e.g., UserRepository, ProductRepository,
 * AddressRepository) should extend this class to avoid repeating common CRUD
 * logic and shared query utilities.
 *
 * Why this exists:
 * - Centralizes reusable repository logic in one place.
 * - Keeps child repositories clean and focused on business-specific logic.
 * - Promotes consistent patterns across the codebase (same CRUD flow, same
 *   model access pattern, same trait utilities).
 *
 * What lives here:
 * 1. Shared Traits
 *    - Traits that provide reusable query features (like pagination, filtering,
 *      searching, sorting, relationship loading, etc.) are placed here so every
 *      child repository automatically gains access to them.
 *    - In this case: BaseQueryTrait.
 *
 * 2. Shared Model Handling
 *    - The repository stores a single Eloquent model instance in $model.
 *    - Child repositories inject their specific model (User, Product, Address, etc.)
 *      via the constructor and pass it to the parent.
 *
 *      Example:
 *      class UserRepository extends BaseRepository {
 *          public function __construct(User $model) {
 *              parent::__construct($model);
 *          }
 *      }
 *
 * 3. Shared CRUD Methods
 *    - find($id): Finds a record or throws 404 (ModelNotFoundException).
 *    - create($data): Creates a record using the model query builder.
 *    - update($id, $data): Finds then updates a record.
 *    - delete($id): Finds then deletes a record.
 *
 * Customization / Overrides:
 * - Child repositories can override any method when special rules are needed.
 *   Example: UserRepository may override update() to hash passwords before saving.
 *
 *   public function update(int $id, array $data)
 *   {
 *       if (!empty($data['password'])) {
 *           $data['password'] = Hash::make($data['password']);
 *       }
 *       return parent::update($id, $data);
 *   }
 *
 * Notes / Best Practice:
 * - Keep BaseRepository generic (no business-specific rules).
 * - Put model-specific or domain-specific rules in:
 *   - the child repository (service/repository-level rules), or
 *   - model mutators/casts (model-level rules, like password hashing).
 */

abstract class BaseRepository
{
    use BaseQueryTrait;

    /**
     * The Eloquent model instance used by the repository.
     */
    protected Model $model;

    /**
     * Inject the model so child repositories automatically share the same pattern.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the repository model instance.
     */
    public function model(): Model
    {
        return $this->model;
    }

    /**
     * Find a record by ID or fail.
     */
    public function find(int $id)
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data)
    {
        return $this->model->newQuery()->create($data);
    }

    /**
     * Update a record by ID.
     */
    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    /**
     * Delete a record by ID.
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);
        $record->delete();
        return true;
    }
}