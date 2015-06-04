<?php
namespace TheSeer\Phive {

    class Factory {

        /**
         * @return CLI
         */
        public function getCLI() {
            return new CLI($this->getCommandLocator());
        }

        /**
         * @return VersionCommand
         */
        public function getVersionCommand() {
            return new VersionCommand($this->getVersion());
        }

        /**
         * @return HelpCommand
         */
        public function getHelpCommand() {
            return new HelpCommand();
        }

        /**
         * @param CLICommandOptions $options
         *
         * @return InstallCommand
         */
        public function getInstallCommand(CLICommandOptions $options) {
            return new InstallCommand(
                $this->getInstallService(),
                new InstallCommandConfig($options)
            );
        }

        /**
         * @return CommandLocator
         */
        private function getCommandLocator() {
            return new CommandLocator($this);
        }

        /**
         * @return Version
         */
        private function getVersion() {
            return new Version();
        }

        /**
         * @return InstallService
         */
        private function getInstallService() {
            return new InstallService(
                $this->getPharIoClient(),
                $this->getPharDownloader()
            );
        }

        /**
         * @return PharIoClient
         */
        private function getPharIoClient() {
            return new PharIoClient();
        }

        /**
         * @return PharDownloader
         */
        private function getPharDownloader() {
            return new PharDownloader();
        }

        /**
         * @return SignatureService
         */
        public function getSignatureService() {
            return new SignatureService(new SignatureVerifierLocator($this));
        }

        /**
         * @return GnupgSignatureVerifier
         */
        public function getGnupgSignatureVerifier() {
            return new GnupgSignatureVerifier($this->getGnupg());
        }

        /**
         * @return KeyService
         */
        public function getKeyService() {
            return new KeyService($this->getPgpKeyDownloader(), $this->getGnupgKeyImporter(), $this->getGnupgKeyRing());
        }

        /**
         * @return GnupgKeyImporter
         */
        private function getGnupgKeyImporter() {
            return new GnupgKeyImporter($this->getGnupg());
        }

        /**
         * @return GnupgKeyDownloader
         */
        private function getPgpKeyDownloader() {
            return new GnupgKeyDownloader($this->getCurl(), include __DIR__ . '/../conf/pgp-keyservers.php');
        }

        /**
         * @return GnupgKeyRing
         */
        private function getGnupgKeyRing() {
            return new GnupgKeyRing($this->getGnupg());
        }

        /**
         * @return Curl
         */
        private function getCurl() {
            return new Curl();
        }

        /**
         * @return \Gnupg
         */
        private function getGnupg() {
            putenv('GNUPGHOME=~/.PHIVE/gpg');
            $gpg = new \Gnupg();
            $gpg->seterrormode(\gnupg::ERROR_EXCEPTION);
            return $gpg;
        }

    }

}