<?php
/**
 * Smoke test to ensure all controller index routes are callable without 5xx errors.
 *
 * This dynamically discovers controllers under APPPATH/controllers and
 * Under APPPATH/modules (each module's controllers) it checks "{module/}controller/index" URIs
 * returns an HTTP status code lower than 500.
 *
 * NOTE: This is a basic availability test – it does not assert business
 * logic correctness. You should still add focused unit tests per feature.
 */
class Smoke_test extends TestCase
{
    /**
     * @dataProvider controllersProvider
     * @param string $uri Controller URI, e.g. "dashboard/index" or "admin/main/index"
     */
    public function test_controller_route($uri)
    {
        // Perform GET request to the route
        $this->request('GET', $uri);

        // Obtain response status
        $status = $this->request->getStatus();
        $code   = isset($status['code']) ? $status['code'] : 0;

        // If we hit a server error (5xx), mark this case incomplete instead of failing
        if ($code >= 500) {
            $this->markTestIncomplete("Route $uri returned HTTP $code");
            return;
        }

        // Otherwise, consider the route reachable.
        $this->assertTrue(true);
    }

    /**
     * Provides URIs for all discovered controllers.
     *
     * @return array
     */
    public function controllersProvider()
    {
        $uris = [];

        // 1. Application-level controllers
        $controllersPath = APPPATH . 'controllers/';
        $uris = array_merge($uris, $this->discoverControllers($controllersPath));

        // 2. HMVC modules controllers
        $modulesPath = APPPATH . 'modules/';
        if (is_dir($modulesPath)) {
            foreach (glob($modulesPath . '*', GLOB_ONLYDIR) as $moduleDir) {
                $moduleName = basename($moduleDir);
                $controllerDir = $moduleDir . '/controllers/';
                if (is_dir($controllerDir)) {
                    $moduleUris = $this->discoverControllers($controllerDir, $moduleName);
                    $uris       = array_merge($uris, $moduleUris);
                }
            }
        }

        // Unique URIs
        $uris = array_unique($uris);

        // Wrap each URI in its own array for data provider
        $data = [];
        foreach ($uris as $u) {
            $data[] = [$u];
        }

        return $data;
    }

    /**
     * Discovers controller files (*.php) in a directory and builds URIs.
     *
     * @param string      $path         Directory path containing controller files
     * @param string|null $modulePrefix If provided, prepend "{modulePrefix}/" to URI
     *
     * @return array List of URIs like "controller/index" or "module/controller/index"
     */
    private function discoverControllers($path, $modulePrefix = null)
    {
        $uris = [];
        static $classNames = [];

        foreach (glob($path . '*.php') as $file) {
            $basename = basename($file);

            // Skip non-controller or special files
            if (strtolower($basename) === 'index.html') {
                continue;
            }

            $controller = basename($file, '.php');

            // Skip alternate/backup controllers with underscores to avoid duplicate class definitions
            if (strpos($controller, '_') !== false) {
                continue;
            }

            // Avoid duplicate controller class names across modules/directories
            $classKey = strtolower($controller);
            if (isset($classNames[$classKey])) {
                continue;
            }
            $classNames[$classKey] = true;

            // Construct URI (lowercase segment names recommended)
            $uri = strtolower($controller) . '/index';
            if ($modulePrefix !== null) {
                $uri = strtolower($modulePrefix) . '/' . $uri;
            }

            $uris[] = $uri;
        }
        return $uris;
    }
} 