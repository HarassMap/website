<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace Harassmap\Domain\Models{
/**
 * Model
 *
 * @property int $id
 * @property string $host
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereHost($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereUpdatedAt($value)
 */
	class Domain extends \Eloquent {}
}

namespace Harassmap\Domain\Models{
/**
 * Model
 *
 * @property int $id
 * @property int $domain_id
 * @property string $content_id
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereContentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereUpdatedAt($value)
 */
	class Content extends \Eloquent {}
}

