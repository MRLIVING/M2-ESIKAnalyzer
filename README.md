# M2-ESIKAnalyzer

We describe here how to set up the Elesticsearch search field with Chinese Analyzer., i.e. [IK Analyzer](https://github.com/medcl/elasticsearch-analysis-ik/tree/master).  
The other purpose is to document our journey of site search optimization and step to the accurate result.

Since Magento version 2.4, Magento requires Elasticsearch to be the catalog search engine. 
TODO...

## Elesticsearch Production Mode Configuration

### [Virtual memory](https://www.elastic.co/guide/en/elasticsearch/reference/current/vm-max-map-count.html)

* set `vm.max_map_count` in `/etc/sysctl.conf` to enable after rebooting
  ```
  vm.max_map_count = 262144
  ```

* , or `sysctl -w vm.max_map_count=262144` to enable this time only


### elesticsearch.yml
```
# ---------------------------------- Network -----------------------------------
# Set the bind address to a specific IP (IPv4 or IPv6):
network.host: 0.0.0.0
#
# --------------------------------- Discovery ----------------------------------
# Pass an initial list of hosts to perform discovery when this node is started:
# The default list of hosts is ["127.0.0.1", "[::1]"]
discovery.seed_hosts: [] 
#
# Bootstrap the cluster using an initial set of master-eligible nodes:
cluster.initial_master_nodes: []
```

### [Install IK Analysis for Elasticsearch](https://github.com/medcl/elasticsearch-analysis-ik/tree/v7.6.2#install)
Please watch the ES version and download the matching plugin version.
```
./bin/elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v7.6.2/elasticsearch-analysis-ik-7.6.2.zip
```

### Restart Elasticsearch demon
```
kill ${ES_PROCESS_ID}
./bin/elasticsearch -d
```

## Elesticsearch Client
### [Chrome extension - ElasticSearch Head](https://chrome.google.com/webstore/detail/elasticsearch-head/ffmkiejjmecolpfloofpjologoblkegm)
### Elesticsearch Top APIs 
* [Get mapping API](https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-get-mapping.html#indices-get-mapping)  
  `GET /${INDEX_NAME}/_mapping`
  
* [Search API](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html#search-search)  
  * [query string syntax](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html#query-string-syntax)    
  `_search?q=name:枕頭`
  


## Magento 2 extension
### Source of the Field Analyzers 
* `_search` [mapping source](https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/AddDefaultSearchField.php#L29-L32)
```
    public function process(array $mapping): array
    {
        return [self::NAME => ['type' => 'text']] + $mapping;
    }
```

* `name` and the others [mapping source](https://github.com/magento/magento2/blob/2.4.2/app/code/Magento/Elasticsearch/Model/Adapter/FieldMapper/Product/FieldProvider/StaticField.php#L202-L216)


## Reference 
* [IK Analysis for Elasticsearch](https://github.com/medcl/elasticsearch-analysis-ik/tree/v7.6.2)
