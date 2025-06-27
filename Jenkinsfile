pipeline {
    agent any

    environment {
        SONARQUBE_SERVER = 'ayoub' 
        DOCKER_IMAGE = "laravel-app:latest"
        DOCKER_REGISTRY = ""
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    try {
                        sh '''
                            if ! command -v composer &> /dev/null; then
                                echo "Installing Composer..."
                                curl -sS https://getcomposer.org/installer | php
                                sudo mv composer.phar /usr/local/bin/composer
                            fi
                            composer install --no-dev --optimize-autoloader
                        '''
                    } catch (Exception e) {
                        echo "Composer install failed: ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        error("Cannot proceed without dependencies")
                    }
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    try {
                        sh 'docker build -t $DOCKER_IMAGE .'
                        echo "Docker image built successfully"
                    } catch (Exception e) {
                        echo "Docker build failed: ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        error("Docker build failed")
                    }
                }
            }
        }

        stage('Trivy Scan') {
            steps {
                script {
                    try {
                        sh '''
                            if ! command -v trivy &> /dev/null; then
                                echo "Installing Trivy..."
                                if command -v apt-get &> /dev/null; then
                                    sudo apt-get update
                                    sudo apt-get install -y wget apt-transport-https gnupg lsb-release
                                    wget -qO - https://aquasecurity.github.io/trivy-repo/deb/public.key | sudo apt-key add -
                                    echo "deb https://aquasecurity.github.io/trivy-repo/deb $(lsb_release -sc) main" | sudo tee -a /etc/apt/sources.list.d/trivy.list
                                    sudo apt-get update
                                    sudo apt-get install -y trivy
                                else
                                    echo "Using Trivy via Docker"
                                    docker run --rm -v /var/run/docker.sock:/var/run/docker.sock \
                                        aquasec/trivy:latest image --exit-code 0 --severity HIGH,CRITICAL $DOCKER_IMAGE
                                    exit 0
                                fi
                            fi
                            trivy image --exit-code 0 --severity HIGH,CRITICAL --format json --output trivy-report.json $DOCKER_IMAGE
                            echo "Trivy scan completed. Check trivy-report.json for details."
                        '''
                    } catch (Exception e) {
                        echo "Trivy scan failed: ${e.getMessage()}"
                        currentBuild.result = 'UNSTABLE'
                    }
                }
            }
            post {
                always {
                    script {
                        if (fileExists('trivy-report.json')) {
                            archiveArtifacts artifacts: 'trivy-report.json', fingerprint: true
                        }
                    }
                }
            }
        }

        stage('Unit Tests') {
            steps {
                script {
                    try {
                        sh '''
                            composer install --dev
                            if [ ! -f .env ]; then
                                cp .env.example .env
                                php artisan key:generate
                            fi
                            vendor/bin/phpunit --testdox
                        '''
                    } catch (Exception e) {
                        echo "Unit tests failed: ${e.getMessage()}"
                        currentBuild.result = 'UNSTABLE'
                    }
                }
            }
            post {
                always {
                    script {
                        if (fileExists('tests/results.xml')) {
                            publishTestResults testResultsPattern: 'tests/results.xml'
                        }
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                script {
                    try {
                        withSonarQubeEnv(SONARQUBE_SERVER) {
                            sh '''
                                vendor/bin/phpunit --coverage-clover=coverage.xml
                                if ! command -v sonar-scanner &> /dev/null; then
                                    echo "Installing SonarQube Scanner..."
                                    wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.8.0.2856-linux.zip
                                    unzip sonar-scanner-cli-4.8.0.2856-linux.zip
                                    sudo mv sonar-scanner-4.8.0.2856-linux /opt/sonar-scanner
                                    sudo ln -s /opt/sonar-scanner/bin/sonar-scanner /usr/local/bin/sonar-scanner
                                fi
                                sonar-scanner \
                                    -Dsonar.projectKey=monprojet \
                                    -Dsonar.php.coverage.reportPaths=coverage.xml \
                                    -Dsonar.sources=app \
                                    -Dsonar.tests=tests \
                                    -Dsonar.host.url=$SONAR_HOST_URL \
                                    -Dsonar.login=$SONAR_AUTH_TOKEN
                            '''
                        }
                    } catch (Exception e) {
                        echo "SonarQube analysis failed: ${e.getMessage()}"
                        currentBuild.result = 'UNSTABLE'
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                script {
                    try {
                        timeout(time: 5, unit: 'MINUTES') {
                            waitForQualityGate abortPipeline: false
                        }
                    } catch (Exception e) {
                        echo "Quality Gate timeout or failed: ${e.getMessage()}"
                        currentBuild.result = 'UNSTABLE'
                    }
                }
            }
        }

        stage('Mutation Tests') {
            steps {
                script {
                    try {
                        sh '''
                            if ! command -v infection &> /dev/null; then
                                composer global require infection/infection
                            fi
                            ~/.composer/vendor/bin/infection --threads=2 --min-msi=80 --min-covered-msi=80 --only-covered
                        '''
                    } catch (Exception e) {
                        echo "Mutation tests failed: ${e.getMessage()}"
                        currentBuild.result = 'UNSTABLE'
                    }
                }
            }
        }

        stage('Deploy') {
            when {
                anyOf {
                    branch 'main'
                    branch 'master'
                }
            }
            steps {
                script {
                    try {
                        sh '''
                            docker stop $(docker ps -q --filter ancestor=$DOCKER_IMAGE) || true
                            docker rm $(docker ps -aq --filter ancestor=$DOCKER_IMAGE) || true
                            docker run -d --name laravel-app-$(date +%s) -p 9000:9000 $DOCKER_IMAGE
                            echo "Application deployed successfully on port 9000"
                        '''
                    } catch (Exception e) {
                        echo "Deployment failed: ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        error("Deployment failed")
                    }
                }
            }
        }
    }

    post {
        always {
            sh '''
                docker system prune -f || true
                rm -rf coverage.xml trivy-report.json || true
            '''
        }
        success {
            echo 'Pipeline completed successfully!'
        }
        failure {
            echo 'Pipeline failed!'
        }
        unstable {
            echo 'Pipeline completed with warnings!'
        }
    }
}
