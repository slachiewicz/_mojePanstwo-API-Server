<?

/**
 * @SWG\Model(id="PostalCode", required="['id','status']",
 *
 * @SWG\Property(name="a",type="Model2", description="description")
 * @SWG\Property(name="b",type="integer", description="description", minimum=0, maximum=10)
 * @SWG\Property(name="c",type="array", description="description", items="$ref:Model2")
 * @SWG\Property(name="d",type="array", description="description",  @SWG\Items("$ref:Model2"))
 * @SWG\Property(name="status",type="string", description="description", enum="['available', 'pending', 'sold']")
 * )
 *
 */
class PostalCode {}