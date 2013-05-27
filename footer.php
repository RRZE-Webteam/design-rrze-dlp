            <div id="footer" role="contentinfo">
                <div id="zusatzinfo">
                <?php if ( ! is_404() ): ?>
                    
                    <?php if( ! dynamic_sidebar( 'sidebar-footer' ) ) : ?>
                        <p>
                            <?php _e( 'Dieser Bereich ist f&uuml;r die Zusatzinformationen vorgesehen. Hier könnten hilfreiche Links oder sonstige Informationen stehen, welche auf jeder Seite eingeblendet werden sollen. Diese Angaben werden bei der Ausgabe auf dem Drucker nicht mit ausgegeben!', '_rrze' ); ?>
                        </p>
                    <?php endif; ?>

                <?php endif; ?>
                </div>

                <div id="seiteninfo">

                </div><!-- #seiteninfo -->


            </div><!-- #footer -->

        </div><!-- #wrapper -->
        <?php wp_footer(); ?>
    </body>

</html>
