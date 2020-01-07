<?php namespace Zenit\Bundle\Ghost\Entity\Component;

use Zenit\Bundle\DBAccess\Component\Filter\Filter;
use Zenit\Bundle\DBAccess\Component\Finder\AbstractFinder;
/**
 * @mixin Ghost
 */
trait GhostRepositoryFacadeTrait{

	/** @return self */
	static final public function pick($id): ?self{
		return static::$model->repository->pick($id);
	}

	/** @return self[] */
	static final public function collect($ids): array{
		return static::$model->repository->collect($ids);
	}

	static final public function search(Filter $filter = null): AbstractFinder{
		return static::$model->repository->search($filter);
	}
}